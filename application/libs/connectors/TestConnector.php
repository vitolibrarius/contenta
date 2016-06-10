<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;

use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

class TestConnectorException extends \Exception {}

class TestConnector extends EndpointConnector
{
	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function testFilename()
	{
		$endpoint = $this->endpoint();
		$base = parse_url($endpoint->base_url, PHP_URL_PATH);
		$testName = $endpoint->name;
		$extension = $endpoint->api_key;

		return appendPath( $base, $testName . "." . $extension);
	}

	public function testConnnector()
	{
		$filename = $this->testFilename();
		$success = is_file($filename);
		return array($success, ($success ? "Found " : "Could not find ") . $filename);
	}

	public function performGET($url, $force = false)
	{
		$extension = $this->endpoint()->api_key;
		$filename = $this->testFilename();

		switch( $extension ) {
			case 'xml':
				libxml_use_internal_errors(true);
				$data = simplexml_load_file($filename);
				$errors = libxml_get_errors();
				libxml_clear_errors();
				break;
			case 'json':
				$data = json_decode(file_get_contents($filename), true);
				$errors = array();
				if ( json_last_error() != 0 ) {
					$errors['errno'] = json_last_error();
					$errors['message'] = jsonErrorString(json_last_error());
				}
				break;
			default:
				die( PHP_EOL . $filename .PHP_EOL );

		}
		return array($data, $errors);
	}
}
