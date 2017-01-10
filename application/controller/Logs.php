<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Config as Config;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;
use \http\PageParams as PageParams;

use \model\user\Users as Users;
use \model\media\logs\Log as Log;
use \model\media\logs\Log_Level as Log_Level;

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
			$this->view->params = Session::pageParameters( $this, "index" );
			$this->view->render( '/logs/index_' . $type);
		}
	}

	function notifications()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->render( '/logs/index_' . $type);
		}
	}

	function purgeMatches()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			$query = $parameters->currentQueryValues();
			$model = Model::Named("Log");
			$qualifier = $model->searchQualifiers($query);
			$delete = SQL::Delete( $model, Qualifier::AndQualifier( $qualifier ));
			$succes = $delete->commitTransaction();
			if ( $succes == false ) {
				Session::addNegativeFeedback("Failed to delete");
			}

			header('location: ' . Config::Web('/Logs/index' ));
		}
	}

	function log_table($pageNum = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			$parameters->setPageSize(12);
			list( $hasNewValues, $query) = $parameters->updateParametersFromGET( array(
				'trace', 'trace_id', 'context', 'context_id', 'level_code', 'message' )
			);

			$model = Model::Named("Log");
			$results = $model->searchQuery( $hasNewValues, $query, $pageNum, $parameters );

			$this->view->model = $model;
			$this->view->params = $parameters;
			$this->view->logArray = $results;
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
				(isset($_GET['level']) && $_GET['level'] != 'any') ? $_GET['level'] : null,
				isset($_GET['message']) ? $_GET['message'] : null
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

			$this->view->pageCurrent = 0;
			$this->view->chunkCount = 0;
			$this->view->logArray = array();
			$this->view->elapsed = "0 seconds";

			if ( $filename != null ) {
				$fullpath = Config::GetLog($filename);
				if ( file_exists($fullpath) && filesize($fullpath) > 0 ) {
					$before = microtime(true);
					$filedata = file_get_contents($fullpath);
					$result = preg_replace("/(\\[(\\d\\d\\.\\d\\d\\.\\d\\d\\d\\d\\s-\\s)(\\d\\d:\\d\\d:\\d\\d)])/uU", "=-=-=-=-=-=-=$3", $filedata);
					$lines = preg_split('/=-=-=-=-=-=-=/', $result, -1, PREG_SPLIT_NO_EMPTY);
					rsort( $lines );
					$chunks = array_chunk ( $lines, 100, true);
					$chunkNum = min( max(0, $chunkNum), (count($chunks) - 1) );
					$chunkIndex = $chunks[$chunkNum];

					$log_array = array();
					foreach( $chunkIndex as $line ) {
						$line = implode("<br>", split_lines($line));
						$matches = array();
						$matchCount = preg_match_all(
							"/(\\d\\d:\\d\\d:\\d\\d)\\s+(info|warning|error)\\s+\\|(((.*):(.*))|(.*))\\|\\s+\\|(((.*):(.*))|(.*))\\|(.*)$/uUms",
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
								"/(\\d\\d:\\d\\d:\\d\\d)\\s+(info|warning|error)\\s+\\|(.*)\\|\\s+(.*)$/uUms",
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
			}

			$this->view->filename= $filename;

			$this->view->render( '/logs/log_file', true);
		}
	}
}
