<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Localized as Localized;
use \Logger as Logger;
use \Config as Config;

use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \utilities\MediaFilename as MediaFilename;

use controller\Admin as Admin;

use \model\user\Users as Users;
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;
/**
 * Class Endpoint
 */
class AdminTest extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			// test=filenames
			$testName = HttpGet::get('test', '');
			if ( method_exists($this, $testName) ) {
				$this->view->listArray = $this->$testName();
			}
			$this->view->testName = $testName;
			$this->view->render( '/admin/tests' );
		}
	}

	function acceptFilenames()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$hash = HttpGet::get('hash', '');
			$test = HttpGet::get('test', 'filenames');
			if ( empty($hash) == false ) {
				$jsonData = $this->getFilenameJson();
				if (isset($jsonData[$hash])) {
					$record = $jsonData[$hash];
					$source = (isset($record["source"]) ? $record["source"] : $hash);
					$mediaFilename = new MediaFilename($source);
					$meta = $mediaFilename->updateFileMetaData();
					$meta["source"] = $source;

					unset($jsonData[$hash]);
					if ( strlen($hash) != 32 || contains(" ", $hash) ) {
						$hash = md5($hash);
					}
					$jsonData[$hash] = $meta;
					$this->saveFilenameJson($jsonData);
// 					echo json_encode($meta, JSON_PRETTY_PRINT);
				}
			}
			header('location: ' . Config::Web('/AdminTest/index' ) . "?test=" . $test);
		}
	}

	function flagFilenames()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$hash = HttpGet::get('hash', '');
			$test = HttpGet::get('test', 'filenames');
			if ( empty($hash) == false ) {
				$jsonData = $this->getFilenameJson();
				if (isset($jsonData[$hash])) {
					$record = $jsonData[$hash];
					$source = (isset($record["source"]) ? $record["source"] : $hash);
					$mediaFilename = new MediaFilename($source);
					$meta = $mediaFilename->updateFileMetaData();
					$meta["source"] = $source;
					$meta["flagged"] = true;

					unset($jsonData[$hash]);
					if ( strlen($hash) != 32 || contains(" ", $hash) ) {
						$hash = md5($hash);
					}
					$jsonData[$hash] = $meta;
					$this->saveFilenameJson($jsonData);
// 					echo json_encode($meta, JSON_PRETTY_PRINT);
				}
			}
			header('location: ' . Config::Web('/AdminTest/index' ) . "?test=" . $test);
		}
	}

	function deleteFilenames()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$hash = HttpGet::get('hash', '');
			$test = HttpGet::get('test', 'filenames');
			if ( empty($hash) == false ) {
				$jsonData = $this->getFilenameJson();
				if (isset($jsonData[$hash])) {
					unset($jsonData[$hash]);
					$this->saveFilenameJson($jsonData);
				}
			}
			header('location: ' . Config::Web('/AdminTest/index' ) . "?test=" . $test);
		}
	}

	private function getFilenameJson()
	{
		$path = appendPath( SYSTEM_PATH, "phpunit", "_resources_", "Filename.json" );
		if (is_file($path)) {
			$jsonData = json_decode(file_get_contents($path), true);
			if ( json_last_error() != 0 ) {
				Logger::logError( 'Last error: ' . jsonErrorString(json_last_error()), get_class($this), 'readMetadata()');
				throw new \Exception( jsonErrorString(json_last_error()) );
			}
		}
		else {
			$jsonData = array("nofile" => $path);
		}
		return $jsonData;
	}

	private function saveFilenameJson($jsonData)
	{
		$path = appendPath( SYSTEM_PATH, "phpunit", "_resources_", "Filename.json" );
		$raw = json_encode($jsonData, JSON_PRETTY_PRINT);
		if ( json_last_error() != 0 ) {
			Logger::logError( 'Last error: ' . jsonErrorString(json_last_error()), get_class($this), 'readMetadata()');
			throw new \Exception( jsonErrorString(json_last_error()) );
		}
		if ( file_put_contents($path, $raw) == false ) {
			echo "file save errors " . $hash;
		}
	}

	private function filenames()
	{
		$jsonData = $this->getFilenameJson();

		$results = array();
		foreach( $jsonData as $idxHash => $expected ) {
			$source = (isset($expected["source"]) ? $expected["source"] : $idxHash);
			$mediaFilename = new MediaFilename($source);
			$meta = $mediaFilename->updateFileMetaData();
			$meta["source"] = $source;

			$result_diff_a = array_diff($meta, $expected);
			$result_diff_b = array_diff($expected, $meta);
			$result_assoc = array_diff_assoc($meta, $expected);

			if ( strlen($idxHash) != 32 || count($result_diff_a) > 0 || count($result_assoc) > 0 || count($result_diff_b) > 0) {
				$data = $meta;
				$errorKeys = array_unique( array_merge(array_keys($result_diff_a), array_keys($result_diff_b), array_keys($result_assoc)) );
				foreach ($errorKeys as $key => $value) {
					$data[$value] = "Expected <b>"
						. (isset($expected[$value]) ? $expected[$value] : 'null') . "</b> but got <em>"
						. (isset($meta[$value]) ? $meta[$value] : 'null') . "</em>";
				}
// 					$fctName = "parsed_";
// 					$i = 0;
// 					$fct = $fctName.$i;
// 					while ( method_exists($mediaFilename, $fct) ){
// 						$clean = $mediaFilename->$fct();
// 						$data[$fct] = $clean;
// 						$i++;
// 						$fct = $fctName.$i;
// 					}

				$results[$idxHash] = $data;
			}
		}
		return $results;
	}

	private function flagged()
	{
		$jsonData = $this->getFilenameJson();

		$results = array();
		foreach( $jsonData as $idxHash => $expected ) {
			$flagged = (isset($expected["flagged"]) ? $expected["flagged"] : false);
			$source = (isset($expected["source"]) ? $expected["source"] : $idxHash);
			$mediaFilename = new MediaFilename($source);
			$meta = $mediaFilename->updateFileMetaData();
			$meta["source"] = $source;

			if (isset($expected["flagged"]) ) { unset($expected["flagged"]); }

			$result_diff_a = array_diff($meta, $expected);
			$result_diff_b = array_diff($expected, $meta);
			$result_assoc = array_diff_assoc($meta, $expected);

			if ( $flagged || count($result_diff_a) > 0 || count($result_assoc) > 0 || count($result_diff_b) > 0) {
				$data = $meta;
				$errorKeys = array_unique( array_merge(array_keys($result_diff_a), array_keys($result_diff_b), array_keys($result_assoc)) );
				foreach ($errorKeys as $key => $value) {
					$data[$value] = "Expected <b>"
						. (isset($expected[$value]) ? $expected[$value] : 'null') . "</b> but got <em>"
						. (isset($meta[$value]) ? $meta[$value] : 'null') . "</em>";
				}

				$results[$idxHash] = $data;
			}
		}
		return $results;
	}
}
