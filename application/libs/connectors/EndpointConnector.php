<?php


namespace connectors;

use \RecursiveIteratorIterator as RecursiveIteratorIterator;
use \RecursiveDirectoryIterator as RecursiveDirectoryIterator;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;
use \Metadata as Metadata;

use model\Users as Users;
use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

class InvalidEndpointConfigurationException extends \Exception {}
class NetworkErrorException extends \Exception {}
class ResponseErrorException extends \Exception {}

/**
 * Class Connector
 */
abstract class EndpointConnector
{
	private $endpoint = null;

	public function __construct($point)
	{
		$endpointModel = Model::Named('Endpoint');
		if ( is_a($point, '\model\EndpointDBO')) {
			if ($point->isEnabled() ) {
				$this->endpoint = $point;
			}
			else {
				throw new InvalidEndpointConfigurationException("Endpoint " . $point->displayName() . " is disabled");
			}
		}
		else {
			throw new InvalidEndpointConfigurationException("Cannot be initialized with "
				. (empty($point) ? '-null-' : get_class($point))
				. ", requires a configuration of type 'endpointDBO'");
		}
	}

	public function debugPath() {
		if ( isset($this->debugPath) == false ) {
			$root = Config::GetProcessing();
			$this->debugPath = appendPath($root, get_short_class($this), $this->endpoint->id );
			makeRequiredDirectory($this->debugPath, 'endpoint debug subdirectory for ' . get_class($this));

			// purge any old files greater then a day
			$objects = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($this->debugPath), RecursiveIteratorIterator::SELF_FIRST);
			foreach($objects as $name => $object) {
				if ( $object->isFile() ) {
					if ($object->getMTime() < (time() - Cache::TTL_DAY)) {
						unlink($object->getPathname());
					}
				}
			}
		}
		return $this->debugPath;
	}

	public function debugData($data, $name)
	{
		if (is_dir($this->debugPath())) {
			$path = appendPath( $this->debugPath(), sanitize_filename($name) );
			file_put_contents( $path, $name . PHP_EOL . PHP_EOL );
			return file_put_contents( $path, $data, FILE_APPEND );
		}
		return false;
	}

	public function isDebuggingResponses() {
		return false;
	}

	public function endpoint()
	{
		return (isset($this->endpoint) ? $this->endpoint : null);
	}

	public function endpointDisplayName() {
		$ep = $this->endpoint();
		if ( $ep!= null && empty( $ep->api_key ) == false ) {
			return $this->endpoint->displayName();
		}
		return null;
	}

	public function endpointAPIKey() {
		$ep = $this->endpoint();
		if ( $ep!= null && empty( $ep->api_key ) == false ) {
			return $this->endpoint->api_key;
		}
		return null;
	}

	public function endpointUsername() {
		$ep = $this->endpoint();
		if ( $ep!= null && empty( $ep->username ) == false ) {
			return $this->endpoint->username;
		}
		return null;
	}

	public function endpointBaseURL() {
		$ep = $this->endpoint();
		if ( $ep!= null && empty( $ep->base_url ) == false ) {
			return $this->endpoint->base_url;
		}
		return null;
	}

	public function endpointEnabled() {
		return (isset($this->endpoint) ? $this->endpoint->isEnabled() : false);
	}

	public function endpointCompressed() {
		return (isset($this->endpoint) ? $this->endpoint->requiresCompression() : false);
	}

	public function cleanURLForLog($url) {
		$clean = urldecode($url);
		if ( empty($url) == false) {
			if ( empty( $this->endpoint->username ) == false ) {
				$clean = str_ireplace($this->endpoint->username, '[username]', $clean);
			}

			if ( empty( $this->endpoint->api_key ) == false ) {
				$clean = str_ireplace($this->endpoint->api_key, '[API_KEY]', $clean);
			}
		}
		return $clean;
	}

	public function performTestConnnector($url)
	{
		$success = true;
		$message = "";
		$data = null;
		try {
			list($data, $headers) = $this->performGET($url, true);
		}
		catch ( \Exception $e ) {
			$success = false;
			$message = "exception " . $e->getMessage();
		}

		return array( $success, $message, $data );
	}

	abstract public function testConnnector();

	public function performPOST( $url, array $postfields = null, array $headers = null)
	{
		echo "perform POST $url" .PHP_EOL;
		if (empty($url) == false && is_array($postfields) && count($postfields) > 0) {
			if ( function_exists('curl_version') == true) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_VERBOSE, true);
// 				curl_setopt($ch, CURLOPT_USERAGENT, APP_NAME . "/" . APP_VERSION);
// 				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt($ch, CURLOPT_AUTOREFERER, true );
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );	# required for https urls
				curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLINFO_HEADER_OUT, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30 );		# seconds to wait for connection
				curl_setopt($ch, CURLOPT_TIMEOUT, 180 );			# seconds to wait for completion
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

				$response = curl_exec($ch);
				$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

				// extract the headers
				$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
				$headers = http_parse_headers(substr($response, 0, $header_size));
				$data = substr($response, $header_size);

// 				$whoami = trim(`whoami`);
// 				$headerLog = new Metadata('/tmp/http_headers_' . $whoami . '.json');
// 				$info = curl_getinfo($ch);
// 				$headerLog->setMeta( $url . "/info", $info );
// 				$headerLog->setMeta( $url . "/headers", $headers );
// 				$headerLog->setMeta( $url . "/cookie", (isset($headers["Set-Cookie"]) ? $headers["Set-Cookie"] : ''));
//
				if ( $http_code >= 200 && $http_code < 300 ) {
					// log?
				}
				else {
					Logger::logError( 'Return code (' . $http_code . '): ' . http_stringForCode($http_code),
							get_class($this), $this->endpointDisplayName());
					Logger::logError( 'Error (' . curl_error($ch) . ') with url: ' . $this->cleanURLForLog($url),
						get_class($this), $this->endpointDisplayName());
					Logger::logError( "Headers " . var_export($headers, true), get_class($this), $this->endpointDisplayName());
				}
				curl_close($ch);
				return array( $data, $headers );
			}
		}
		return false;
	}

	/* perform the URL request and return the data
	*/
	public function performGET($url, $force = false)
	{
		if (empty($url) == false) {
			$cacheData = Cache::MakeKey( $url, "data" );
			$cacheHeaders = Cache::MakeKey( $url, "headers" );
			// check the cashe for the data, there is no point in calling the same API call multiple times in one day
			if ( $force == false ) {
				$data = Cache::Fetch( $cacheData, false, Cache::TTL_DAY );
				$headers = Cache::Fetch( $cacheHeaders, false, Cache::TTL_DAY );
				if ( $data != false ) {
					return array($data, $headers);
				}
			}

			if ( function_exists('curl_version') == true) {
// 				$cacheKey = Cache::MakeKey( "Cookies", parse_url($url, PHP_URL_HOST));
// 				$cookie = Cache::Fetch( $cacheKey, "" );

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
// 				curl_setopt($ch, CURLOPT_USERAGENT, APP_NAME . "/" . APP_VERSION);
// 				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt($ch, CURLOPT_AUTOREFERER, true );
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );	# required for https urls
				curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLINFO_HEADER_OUT, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30 );		# seconds to wait for connection
				curl_setopt($ch, CURLOPT_TIMEOUT, 180 );			# seconds to wait for completion
				curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");

				try {
					$response = curl_exec($ch);
					$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

					// extract the headers
					$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
					$headers = http_parse_headers(substr($response, 0, $header_size));
					$data = substr($response, $header_size);

	// 				$whoami = trim(`whoami`);
	// 				$headerLog = new Metadata('/tmp/http_headers_' . $whoami . '.json');
	// 				$info = curl_getinfo($ch);
	// 				$headerLog->setMeta( $url . "/info", $info );
	// 				$headerLog->setMeta( $url . "/headers", $headers );
	// 				$headerLog->setMeta( $url . "/cookie", (isset($headers["Set-Cookie"]) ? $headers["Set-Cookie"] : ''));

					if ( $http_code >= 200 && $http_code < 300 ) {
						Cache::Store( $cacheHeaders, $headers );
						Cache::Store( $cacheData, $data );
					}
					else {
						Logger::logError( 'Return code (' . $http_code . '): ' . http_stringForCode($http_code),
								get_class($this), $this->endpointDisplayName());
						Logger::logError( 'Error (' . curl_error($ch) . ') with url: ' . $this->cleanURLForLog($url),
							get_class($this), $this->endpointDisplayName());
						throw new ResponseErrorException(curl_error($ch));
					}
				}
				finally {
					curl_close($ch);
				}
			}
			else if ( $this->endpointCompressed() == false) {
				$data = file_get_contents($url);
				$headers = $http_response_header;
				if ( $data == false ) {
					Logger::logError( 'Error (?) with url: ' . $this->cleanURLForLog($url),
						get_class($this), $this->endpointDisplayName());
					Logger::logError( "Headers " . var_export($headers, true), get_class($this), $this->endpointDisplayName());
				}
			}
			else {
				Logger::logError( 'Unable to process compressed url: ' . $this->cleanURLForLog($url),
					get_class($this), $this->endpointDisplayName());
			}

			return array( $data, $headers );
		}
		return false;
	}
}

