<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \CurlFile as CurlFile;

use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

use utilities\MediaFilename as MediaFilename;
use exceptions\EndpointConnectionException as EndpointConnectionException;

class SABnzbdException extends \Exception {}

class SABnzbdConnector extends JSON_EndpointConnector
{
	const PRIORITY_Default = -100;
	const PRIORITY_Paused = -2;
	const PRIORITY_Low = -1;
	const PRIORITY_Normal = 0;
	const PRIORITY_High = 1;
	const PRIORITY_Force = 2;

	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function testConnnector()
	{
		$success = false;
		$message = "";

		$params = $this->defaultParameters();
		$params['mode'] = "qstatus";
		$status_url = $this->endpointBaseURL() . "?" . http_build_query($params);
		list( $success, $message, $json ) = $this->performTestConnnector($status_url);
		if (is_array($json) && isset($json['status'])) {
			$success = $json['status'];
			if ( $success == false ) {
				$message = (isset($json['error']) ? $json['error'] : "unknown connection error");
			}
		}

		return array($success, $message);
	}

	public function defaultParameters() {
		$defaultParam = array();
		$defaultParam["output"] = "json";

		if ( empty($this->endpointAPIKey()) == false ) {
			$defaultParam["apikey"] = $this->endpointAPIKey();
		}
		return $defaultParam;
	}

	public function simpleRequestForMode( $mode = '' )
	{
		$params = $this->defaultParameters();
		$params['mode'] = $mode;

		$status_url = $this->endpointBaseURL() . "?" . http_build_query($params);
		list($details, $headers) = $this->performGET( $status_url );
		return $details;
	}

	public function statusCheck()
	{
		//http://localhost:8080/api?mode=qstatus&output=json&apikey=711de66ca57dae90338267d05f70efe9
		return $this->simpleRequestForMode( 'qstatus' );
	}

	public function restart()
	{
		//http://localhost:8080/sabnzbd/api?mode=restart
		return $this->simpleRequestForMode( 'restart' );
	}

	public function shutdown()
	{
		//http://localhost:8080/sabnzbd/api?mode=shutdown
		return $this->simpleRequestForMode( 'shutdown' );
	}

	public function pauseAll()
	{
		//http://localhost:8080/sabnzbd/api?mode=pause
		return $this->simpleRequestForMode( 'pause' );
	}

	public function resumeAll()
	{
		//http://localhost:8080/sabnzbd/api?mode=resume
		return $this->simpleRequestForMode( 'resume' );
	}

	public function sabnzbdVersion()
	{
		// http://localhost:8080/sabnzbd/api?mode=version&output=xml
		return $this->simpleRequestForMode( 'version' );
	}

	public function categories()
	{
		//http://localhost:8080/sabnzbd/api?mode=get_cats&output=xml
		$json = $this->simpleRequestForMode( 'get_cats' );
		return (is_array($json) && isset($json['categories']) ? $json['categories'] : null);
	}

	public function comicCategory()
	{
		$category = null;
		$all = $this->categories();
		foreach($all as $value) {
			if (preg_match("/(comics?)/uiU",$value)) {
				$category = $value;
				break;
			}
		}
		return $category;
	}

	public function scripts()
	{
		//http://localhost:8080/sabnzbd/api?mode=get_scripts&output=json
		$json = $this->simpleRequestForMode( 'get_scripts' );
		return (is_array($json) && isset($json['scripts']) ? $json['scripts'] : null);
	}

	public function contentaScript()
	{
		$script = null;
		$all = $this->scripts();
		foreach($all as $value) {
			if (preg_match("/(contenta)/uiU",$value)) {
				$script = $value;
				break;
			}
		}
		return $script;
	}

	public function queue()
	{
		//http://localhost:8080/sabnzbd/api?mode=queue&output=xml
		$json = $this->simpleRequestForMode( 'queue' );
// 		file_put_contents( "/tmp/sabtest_queue.json", json_encode($json, JSON_PRETTY_PRINT));
		return $json;
	}

	public function queueSlots()
	{
		$queue = $this->queue();
		if ( is_array( $queue ) && isset($queue['queue'], $queue['queue']['slots'])) {
			return $queue['queue']['slots'];
		}
		return $queue;
	}

	public function history()
	{
		//http://localhost:8080/sabnzbd/api?mode=history&output=json
		$json = $this->simpleRequestForMode( 'history' );
// 		file_put_contents( "/tmp/sabtest_history.json", json_encode($json, JSON_PRETTY_PRINT));
		return $json;
	}

	public function historySlots()
	{
		$history = $this->history();
		if ( is_array( $history ) && isset($history['history'], $history['history']['slots'])) {
			return $history['history']['slots'];
		}
		return $history;
	}

	public function historyDelete()
	{
		if (func_num_args() == 0) {
			Logger::logError( 'Unable to delete SABnzbd history with -null- identifiers',
				get_short_class($this), $this->endpoint());
			return false;
		}

		// http://localhost:8080/sabnzbd/api?mode=history&name=delete&value=SABnzbd_nzo_df2hyd,SABnzbd_nzo_op3shfs
		$keys = func_get_args();
		$teeth = array_filter($keys, 'is_string');
		$params = $this->defaultParameters();
		$params['mode'] = 'history';
		$params['name'] = 'delete';
		$params['del_files'] = '1';
		$params['value'] = implode(',', $teeth);

		$status_url = $this->endpointBaseURL() . "?" . http_build_query($params);
		list($details, $headers) = $this->performGET( $status_url );
		return $details;
	}

	/**
		Queue management
	*/
	public function queueDelete()
	{
		if (func_num_args() == 0) {
			Logger::logError( 'Unable to delete SABnzbd history with -null- identifiers',
				get_short_class($this), $this->endpoint());
			return false;
		}

		// http://localhost:8080/sabnzbd/api?mode=queue&name=delete&value=SABnzbd_nzo_df2hyd,SABnzbd_nzo_op3shfs
		$keys = func_get_args();
		$teeth = array_filter($keys, 'is_string');
		$params = $this->defaultParameters();
		$params['mode'] = 'queue';
		$params['name'] = 'delete';
		$params['value'] = implode(',', $teeth);

		$status_url = $this->endpointBaseURL() . "?" . http_build_query($params);
		list($details, $headers) = $this->performGET( $status_url );
		return $details;
	}

	public function addURL( $url = null, $category = null, $priority = SABnzbdConnector::PRIORITY_Default  )
	{
		/* http://localhost:8080/sabnzbd/api?
			mode=addurl
			&name=http://www.example.com/example.nzb
			&script=customscript.cmd
			&cat=Example
			&priority=-1
			&nzbname=NiceName
		script, cat and priority are all optional. This example adds the nzb into the queue marked as low priority, assigned with a categoriy of "Example", to execute "customscript.cmd" once finished, and with the unpacking option 3 (Repair, Unpack and Delete)
Allows full nzbmatrix links (no need to parse out the ID).
		*/
		if ( is_string($url) ) {
			if ( is_null($category) ) {
				$category = $this->comicCategory();
			}
			$contenta = $this->contentaScript();

			$params = $this->defaultParameters();
			$params['mode'] = 'addurl';
			$params['name'] = $url;
			$params['priority'] = $priority;
			if ( is_string($category) ) {
				$params['cat'] = $category;
			}
			if ( is_string($contenta) ) {
				$params['script'] = $contenta;
			}

			$status_url = $this->endpointBaseURL() . "?" . http_build_query($params);
			list($details, $headers) = $this->performGET( $status_url );
			return $details;
		}
		return false;
	}

	public function addNZB( $filepath = null, $name = null, $category = null, $priority = SABnzbdConnector::PRIORITY_Default  )
	{
		$url = $this->endpointBaseURL();
		if ( file_exists($filepath) ) {
			if ( is_null($category) ) {
				$category = $this->comicCategory();
			}
			$contenta = $this->contentaScript();

			if ( is_null( $name ) ) {
				$name = basename( $filepath );
			}

			$params = $this->defaultParameters();
			$params['mode'] = 'addfile';
			$params['priority'] = $priority;
			$params['nzbname'] = $name;

			if ( is_string($category) ) {
				$params['cat'] = $category;
			}
			if ( is_string($contenta) ) {
				$params['script'] = $contenta;
			}

			$params['nzbfile'] = new CurlFile($filepath, 'application/x-nzb', $name);
			$headers[] = 'Content-Type:multipart/form-data';

			list( $data, $headers ) = parent::performPOST($url, $params, $headers);
			return $data;
		}
		else {
			Logger::logError( 'No file at "' . $filepath
				. '" with URL: ' . $this->cleanURLForLog($url), get_short_class($this), $this->endpoint());
			throw new EndpointConnectionException('No file at "' . $filepath, -1 );
		}

		return false;
	}

	public function performGET($url, $force = true)
	{
		list($json, $headers) = parent::performGET($url, $force);
		if ( $json != false )
		{
			if ( isset($json['status']) && $json['status'] == false)
			{
				Logger::logError( 'Error connecting to SABnzbd "' . $json['error']
					. '" with URL: ' . $this->cleanURLForLog($url), get_short_class($this), $this->endpoint());
				throw new EndpointConnectionException( $json['error'], $json['status'] );
			}
			else
			{
				return array($json, $headers);
			}
		}
		return array(false, null);
	}
}
