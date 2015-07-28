<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Session as Session;
use \Auth as Auth;
use \Config as Config;

use model\Users as Users;
use model\Log as Log;
use model\Log_Level as Log_Level;

/**
 * Class Error
 * The index controller
 */
class Logs extends Controller
{
	/**
	 * Handles what happens when user moves to URL/index/index, which is the same like URL/index or in this
	 * case even URL (without any controller/action) as this is the default controller-action when user gives no input.
	 */
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$type = Config::Get("Logging/type");
			$this->view->render( '/logs/index_' . $type);
		}
	}

	function log_table()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$log_model = Model::Named("Log");
			$this->view->logArray = $log_model->mostRecentLike(
				isset($_GET['trace']) ? $_GET['trace'] : null,
				isset($_GET['trace_id']) ? $_GET['trace_id'] : null,
				isset($_GET['context']) ? $_GET['context'] : null,
				isset($_GET['context_id']) ? $_GET['context_id'] : null,
				isset($_GET['level']) ? $_GET['level'] : null,
				isset($_GET['message']) ? $_GET['message'] : null
			);
			$this->view->render( '/logs/log_table', true);
		}
	}

	function log_inline()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$log_model = Model::Named("Log");
			$this->view->logArray = $log_model->mostRecentLike(
				isset($_GET['trace']) ? $_GET['trace'] : null,
				isset($_GET['trace_id']) ? $_GET['trace_id'] : null,
				isset($_GET['context']) ? $_GET['context'] : null,
				isset($_GET['context_id']) ? $_GET['context_id'] : null,
				isset($_GET['level']) ? $_GET['level'] : null,
				isset($_GET['message']) ? $_GET['message'] : null,
				"desc",
				isset($_GET['limit']) ? $_GET['limit'] : null
			);
			$this->view->render( '/logs/log_inline', true);
		}
	}

	function log_file($chunkNum = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$filename = isset($_GET['filename']) ? $_GET['filename'] : null;
// 				isset($_GET['trace']) ? $_GET['trace'] : null,
// 				isset($_GET['trace_id']) ? $_GET['trace_id'] : null,
// 				isset($_GET['context']) ? $_GET['context'] : null,
// 				isset($_GET['context_id']) ? $_GET['context_id'] : null,
// 				isset($_GET['level']) ? $_GET['level'] : null,
// 				isset($_GET['message']) ? $_GET['message'] : null

			if ( $filename != null ) {
				$before = microtime(true);

				$filedata = file_get_contents(Config::GetLog($filename));
				$result = preg_replace("/(\\[(\\d\\d\\.\\d\\d\\.\\d\\d\\d\\d\\s-\\s)(\\d\\d:\\d\\d:\\d\\d)])/uU", "=-=-=-=-=-=-=$3", $filedata);
				$lines = preg_split('/=-=-=-=-=-=-=/', $result, -1, PREG_SPLIT_NO_EMPTY);
				$chunks = array_chunk ( $lines, 100, true);
				$chunkNum = min( max(0, $chunkNum), (count($chunks) - 1) );
				$chunkIndex = $chunks[$chunkNum];

				$log_array = array();
				foreach( $chunkIndex as $line ) {
					$matches = array();
					$matchCount = preg_match_all(
						"/(\\d\\d:\\d\\d:\\d\\d)\\s+(info|warning|error)\\s+\\|(((.*):(.*))|(.*))\\|\\s+\\|(((.*):(.*))|(.*))\\|(.*)$/uUm",
						$line,
						$matches
					);
					if ( $matchCount == 1 ) {
						$log_array[] = array(
							"time" => $matches[1][0],
							"type" => $matches[2][0],
							"trace" => (isset($matches[5][0]) ? $matches[5][0] : $matches[7][0]),
 							"trace_id" => $matches[6][0],
							"context" => (isset($matches[10][0]) ? $matches[10][0] : $matches[12][0]),
							"context_id" => $matches[11][0],
							"message" => $matches[13][0],
						);
					}
					else {
						$matchCount = preg_match_all(
							"/(\\d\\d:\\d\\d:\\d\\d)\\s+(info|warning|error)\\s+\\|(.*)\\|\\s+(.*)$/uUm",
							$line,
							$matches
						);
						if ( $matchCount == 1 ) {
							$log_array[] = array(
								"time" => $matches[1][0],
								"type" => $matches[2][0],
								"trace" => $matches[3][0],
								"message" => $matches[4][0],
							);
						}
						else {
							$log_array[] = $line;
						}
					}
				}

				$this->view->pageCurrent = $chunkNum;
				$this->view->chunkCount = count($chunks);
				$this->view->logArray = $log_array;

				$after = microtime(true);
				$this->view->elapsed= ($after-$before) . " seconds";
			}

			$this->view->filename= $filename;

			$this->view->render( '/logs/log_file', true);
		}
	}
}
