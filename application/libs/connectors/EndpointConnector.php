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

use \utilities\EndpointThrottle as EndpointThrottle;
use \utilities\EndpointRequestCounter as EndpointRequestCounter;

use \model\user\Users as Users;
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

class InvalidEndpointConfigurationException extends \Exception {}
class NetworkErrorException extends \Exception {}
class ResponseErrorException extends \Exception {}

/**
 * Class Connector
 */
abstract class EndpointConnector
{
	private $debug = false;
	private $endpoint = null;
	private $throttle = null;
	private $maxRequests = null;

	public function __construct($point)
	{
		$endpointModel = Model::Named('Endpoint');
		if ( is_a($point, '\model\network\EndpointDBO')) {
			$this->endpoint = $point;
		}
		else {
			throw new InvalidEndpointConfigurationException("Cannot be initialized with "
				. (empty($point) ? '-null-' : get_class($point))
				. ", requires a configuration of type 'endpointDBO'");
		}
	}

	public function __toString()
	{
		return get_class($this) . " for " . $this->endpointDisplayName();
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
						safe_unlink($object->getPathname());
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

	public function isDebuggingResponses()
	{
		return $this->debug;
	}

	public function setDebuggingResponses( $yesNo = false )
	{
		$this->debug = boolValue( $yesNo, false );
	}

	public function endpoint()
	{
		return (isset($this->endpoint) ? $this->endpoint : null);
	}

	public function endpointDisplayName() {
		$ep = $this->endpoint();
		return ( is_null($ep) ? "- no enpoint -" : $ep->displayName());
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
		return (isset($this->endpoint) ? $this->endpoint->isCompressed() : false);
	}

	public function endpointType() {
		if ( isset($this->endpoint) ) {
			return $this->endpoint->endpointType();
		}
		return null;
	}

	public function endpointParameters()
	{
		if ( isset($this->endpoint) ) {
			return $this->endpoint->jsonParameters();
		}
		return null;
	}

	public function refreshEndpointParameters()
	{
		// noop
	}

	public function endpointParameterForKey($key = null)
	{
		if ( isset($key) && is_null($key) == false ) {
			return array_valueForKeypath( $key, $this->endpointParameters() );
		}
		return null;
	}

	public function setEndpointParamameterForKey( $key = null, $value = null )
	{
		if ( isset($key) && is_null($key) == false ) {
			$json = array_setValueForKeypath($key, $value, $this->endpointParameters());
			$this->endpoint->setJsonParameters($json);
			$this->endpoint->saveChanges();
		}
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

	public function throttleRequestIfRequired() {
		if ( is_null( $this->throttle ) ) {
			$type = $this->endpointType();
			if ( is_a($type, '\model\network\Endpoint_TypeDBO')) {
				$this->throttle = new EndpointThrottle($type->code, $type->throttle_hits, $type->throttle_time);
			}
		}

		if ( is_null( $this->throttle ) == false ) {
			$this->throttle->throttle();
		}
	}

	public function overDailyMaximum() {
		if ( isset($this->endpoint) ) {
			return $this->endpoint->markOverMaximum();
		}
		return false;
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
			$message = $e->getMessage();
		}

		return array( $success, $message, $data );
	}

	abstract public function testConnnector();

	public function performPOST( $url, array $postfields = null, array $headers = null)
	{
		if ( $this->endpointEnabled() != true ) {
			throw new InvalidEndpointConfigurationException("Endpoint " . $point->displayName() . " is disabled");
		}

		if (filter_var($url, FILTER_VALIDATE_URL) == false) {
			throw new \Exception("Invalid url requested [" . var_export($url, true) . "]" );
		}

		if ( $this->overDailyMaximum() ) {
			Logger::logError( "Over the daily maximum for :" . $this->endpointDisplayName());
			return array( null, null );
		}

		$this->throttleRequestIfRequired();
		if ( is_array($postfields) && count($postfields) > 0) {
			if ( function_exists('curl_version') == true) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				curl_setopt($ch, CURLOPT_USERAGENT, CONTENTA_USER_AGENT);
// 				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt($ch, CURLOPT_AUTOREFERER, true );
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );	# required for https urls
				curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
				curl_setopt($ch, CURLOPT_HEADER, true);
				curl_setopt($ch, CURLINFO_HEADER_OUT, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5 );		# seconds to wait for connection
				curl_setopt($ch, CURLOPT_TIMEOUT, 30 );			# seconds to wait for completion
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
					$this->endpoint()->clearErrorCount();
					// log?
				}
				else {
					$this->endpoint()->increaseErrorCount();
					throw new ResponseErrorException('Return code (' . $http_code . '): '
						. http_stringForCode($http_code) . " "
						. curl_error($ch)
					);
// 					Logger::logError( 'Return code (' . $http_code . '): ' . http_stringForCode($http_code),
// 							get_class($this), $this->endpointDisplayName());
// 					Logger::logError( 'Error (' . curl_error($ch) . ') with url: ' . $this->cleanURLForLog($url),
// 						get_class($this), $this->endpointDisplayName());
// 					Logger::logError( "Headers " . var_export($headers, true), get_class($this), $this->endpointDisplayName());
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
		if ( $this->endpointEnabled() != true ) {
			throw new \Exception("Endpoint " . $this->endpointDisplayName() . " is disabled");
		}

		if (filter_var($url, FILTER_VALIDATE_URL) == false) {
			throw new \Exception("Invalid url requested [" . var_export($url, true) . "]" );
		}

		if ( $this->overDailyMaximum() ) {
			Logger::logError( "Over the daily maximum for :" . $this->endpointDisplayName());
			return array( null, null );
		}

		$this->throttleRequestIfRequired();
		$cacheData = Cache::MakeKey( $url, "data" );
		$cacheHeaders = Cache::MakeKey( $url, "headers" );
		$data = null;
		$headers = null;

		// check the cache for the data, there is no point in calling the same API call multiple times in one day
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
			curl_setopt($ch, CURLOPT_USERAGENT, CONTENTA_USER_AGENT);
// 				curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie );
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($ch, CURLOPT_AUTOREFERER, true );
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false );	# required for https urls
			curl_setopt($ch, CURLOPT_MAXREDIRS, 10 );
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5 );		# seconds to wait for connection
			curl_setopt($ch, CURLOPT_TIMEOUT, 30 );			# seconds to wait for completion
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
					$this->endpoint()->clearErrorCount();
					$this->endpoint = Model::Named("Endpoint")->refreshObject($this->endpoint());
					Cache::Store( $cacheHeaders, $headers );
					Cache::Store( $cacheData, $data );
				}
				else if ( $http_code == 302 ) {
					throw new ResponseErrorException('Return code (' . $http_code . '): '
						. var_export($headers, true)
					);
				}
				else {
					$this->endpoint()->increaseErrorCount();
					$this->endpoint = Model::Named("Endpoint")->refreshObject($this->endpoint());
// 						throw new ResponseErrorException('Return code (' . $http_code . '): '
// 							. http_stringForCode($http_code) . " "
// 							. curl_error($ch)
// 						);
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
}

