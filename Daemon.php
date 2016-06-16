<?php

$system_path = dirname(__FILE__);
if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
}

define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);
define('Contenta_Daemon', true );

require APPLICATION_PATH .'config/bootstrap.php';
require APPLICATION_PATH .'config/autoload.php';
require APPLICATION_PATH .'config/common.php';
require APPLICATION_PATH .'config/errors.php';

use \Config as Config;
use \Logger as Logger;
use \Processor as Processor;
use \Model as Model;
use \Metadata as Metadata;

use utilities\Lock as Lock;

define('CONFIG_OVERRIDE', 'ConfigOverride');

function daemon_die($message = '') {
	Logger::logError('Daemon dying ' . $message );
	die( $message );
}

declare(ticks = 1);
function signal_handler($signo) {
	Logger::logError('Received signal ' . $signo );
	if ($signo == SIGTERM || $signo == SIGHUP || $signo == SIGINT) {
		if ( isset($lock) ) {
			$lock->unlock();
		}

		Logger::logError( 'exiting daemon' );
		exit();
	}
}

if ( function_exists('pcntl_signal')) {
	// These define the signal handling
	pcntl_signal(SIGTERM, "signal_handler");
	pcntl_signal(SIGHUP,  "signal_handler");
	pcntl_signal(SIGINT, "signal_handler");
}

/* Change the configuration to custom values, or override specific items.
	Most common use is for testing
 */
function ConfigOverride( Config $config, Array $custom = array() )
{
	if ( array_valueForKeypath( "Repository/path", $custom ) != null ) {
		$config->setValue("Repository/path", array_valueForKeypath( "Repository/path", $custom ) );
	}

	if ( array_valueForKeypath( "Database/type", $custom ) != null ) {
		$config->setValue("Database/type", array_valueForKeypath( "Database/type", $custom ) );
		$config->setValue("Database/path", array_valueForKeypath( "Database/path", $custom ) );
	}

	if ( array_valueForKeypath( "Logging/type", $custom ) != null ) {
		$config->setValue("Logging/type", array_valueForKeypath( "Logging/type", $custom )) || daemon_die("Failed to change the configured Logging");
		$config->setValue("Logging/path", array_valueForKeypath( "Logging/path", $custom )) || daemon_die("Failed to change the configured Logging");
	}

	if ( array_valueForKeypath( "Repository/cache", $custom ) != null ) {
		$config->setValue("Repository/cache", array_valueForKeypath( "Repository/cache", $custom ) );
	}

	if ( array_valueForKeypath( "Repository/processing", $custom ) != null ) {
		$config->setValue("Repository/processing", array_valueForKeypath( "Repository/processing", $custom ) );
	}

	Logger::logInfo( "** Configuration Overrides" );
		Logger::logInfo( "Repository " . $config->repositoryDirectory() );
		Logger::logInfo( "media " . $config->mediaDirectory() );
		Logger::logInfo( "Database " . $config->absolutePathValue("Database/path"));
		Logger::logInfo( "logs " . $config->loggingDirectory() );
		Logger::logInfo( "cache " . $config->cacheDirectory() );
		Logger::logInfo( "processing " . $config->processingDirectory() );
}

/**
 * Capture all output
 */
ob_start();

$config = Config::instance();
$options = getopt("d:");
$workingDir = (isset($options['d']) ? $options['d'] : null);

is_dir($workingDir) || daemon_die( "Unable to find $workingDir" );
$metadata = Metadata::forDirectory( $workingDir );

$processorName = $metadata->getMeta( "processorName" );
is_string($processorName) || daemon_die( "No processor specified" );

if ( $metadata->isMeta( CONFIG_OVERRIDE ) ) {
	ConfigOverride( $config, $metadata->getMeta( CONFIG_OVERRIDE ));
}

$user_api = $metadata->getMeta( "user_api" );
$user = Model::Named("Users")->objectForApi_hash($user_api);

$guid = ( ($metadata->isMeta("guid")) ? $metadata->getMeta("guid") : uuid());
$job_id = ( ($metadata->isMeta("job_id")) ? $metadata->getMeta("job_id") : null);
$debug = ( ($metadata->isMeta("debug")) ? $metadata->getMeta("debug") : null);

/** Configuration environment **/
echo "Daemon startup" .PHP_EOL;
echo "Processor: " . $processorName .PHP_EOL;
echo "User: " . $user .PHP_EOL;
echo "guid: " . $guid .PHP_EOL;
echo "job_id: " . $job_id .PHP_EOL;
echo "Memory: " . ini_get('memory_limit') .PHP_EOL;
echo PHP_EOL;

Logger::instance()->setTrace("Daemon", (is_null($job_id) ? $guid : $job_id) );

// collect the startup logs, then restart the buffer
$log_startup = ob_get_clean();
ob_start();

// Logger::logInfo('starting daemon ', $processorName, ($user ? $user->__toString() : $user_api) );
try {
	$job_model = Model::Named('Job');
	$job_run_model = Model::Named('Job_Running');
	$jobRunning = null;
	$jobObj = null;
	$jobtypeObj = null;
	$jobList = null;
	$pid = getmypid();

	// ensure a job is not already running
	if ( is_null($job_id) ) {
		$jobList = $job_run_model->allForProcessorGUID($processorName, $guid );
	}
	else {
		$jobObj = $job_model->objectForId( $job_id );
		if ( $jobObj instanceof \model\jobs\JobDBO ) {
			Logger::instance()->setTrace($jobObj->displayName(), $job_id);
			$jobtypeObj = $jobObj->jobType();
			$jobList = $job_run_model->allForJob($jobObj );
		}
	}

	if ( is_array($jobList) == false || count($jobList) == 0) {
		list($jobRunning, $errors) = $job_run_model->createObject( array(
			"job" => $jobObj,
			"jobType" => $jobtypeObj,
			"processor" => $processorName,
			"guid" => $guid,
			"pid" => $pid
			)
		);
// 		echo "Job running " . var_export($jobRunning, true) .PHP_EOL;

		try {
			$processor = Processor::Named( $processorName, $guid );
			if ( null != $jobObj ) {
				$endpoint = $jobObj->endpoint();
				if ( $endpoint instanceof model\EndpointDBO ) {
					if (method_exists($processor, "setEndpoint")) {
						$processor->setEndpoint( $endpoint );
					}
					else {
						Logger::logError('Job ' . $jobObj . ' has endpoint but processor '
							. $processorName . ' does not implement setEndpoint()');
					}
				}

				$processor->initializationParams($jobObj->jsonParameters());
			}

			$jobRunning->setDesc( $processor->jobDescription() );
			$jobRunning->saveChanges();

			$processor->processData();

			if ( null != $jobObj ) {
				// success, calc next job schedule run
				$jobObj->setLast_run(time());
				$jobObj->setFail_count(0);
				$jobObj->setElapsed($jobRunning->elapsedSeconds());
				$jobObj->saveChanges();
			}
		}
		catch (Exception $e) {
			Logger::logError('Exception ' . $e->getMessage() . ' ' . $e->getTraceAsString() );
			if ( null != $jobObj ) {
				// disable job???
				// count failures, disable on 5th fail?
				// $jobObj->{"enabled"}(Model::TERTIARY_FALSE);
				$job_model->updateFailure( $jobObj, time() );
			}
		}
		if ( $jobRunning instanceof \model\jobs\Job_RunningDBO ) {
			$job_run_model->deleteObject($jobRunning);
		}
	}
	else {
		Logger::logError('Daemon blocked by running processes ' . var_export($jobList, true));
	}
}
catch ( ClassNotFoundException $exception ) {
	Logger::logException( $exception );
	Logger::logError( 'Failed to find processor ' . $options['p'] );
}

if ( is_null($debug) && is_sub_dir($workingDir, $config->processingDirectory()) ) {
	destroy_dir($workingDir);
}

$log = ob_get_clean();
if ( empty($log) == false ) {
	// only log if something was generated after startup
	Logger::logInfo( $log_startup . $log, "OutputBuffer", $pid );
}

// Logger::logInfo( 'finished daemon', $processorName, ($user ? $user->__toString() : $user_api));

?>
