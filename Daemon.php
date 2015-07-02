<?php

$system_path = dirname(__FILE__);
if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
}

define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

require APPLICATION_PATH .'config/bootstrap.php';
require APPLICATION_PATH .'config/autoload.php';
require APPLICATION_PATH .'config/common.php';
require APPLICATION_PATH .'config/errors.php';

use \Config as Config;
use \Logger as Logger;
use \Processor as Processor;
use \Model as Model;
use utilities\Metadata as Metadata;
use utilities\Lock as Lock;

define('CONFIG_OVERRIDE', 'ConfigOverride');

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
		$config->setValue("Logging/type", array_valueForKeypath( "Logging/type", $custom )) || die("Failed to change the configured Logging");
		$config->setValue("Logging/path", array_valueForKeypath( "Logging/path", $custom )) || die("Failed to change the configured Logging");
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

$config = Config::instance();
$options = getopt("d:");
$workingDir = (isset($options['d']) ? $options['d'] : null);

is_dir($workingDir) || die( "Unable to find $workingDir" );
$metadata = Metadata::forDirectory( $workingDir );

$processorName = $metadata->getMeta( "processorName" );
is_string($processorName) || die( "No processor specified" );

if ( $metadata->isMeta( CONFIG_OVERRIDE ) ) {
	ConfigOverride( $config, $metadata->getMeta( CONFIG_OVERRIDE ));
}

$user_api = $metadata->getMeta( "user_api" );;
$guid = ( ($metadata->isMeta("guid")) ? $metadata->getMeta("guid") : uuid());
$job_id = ( ($metadata->isMeta("job_id")) ? $metadata->getMeta("job_id") : null);
$debug = ( ($metadata->isMeta("debug")) ? $metadata->getMeta("debug") : null);

Logger::instance()->setTrace("Daemon", $job_id );

Logger::logInfo('starting daemon ');
try {
	$job_run_model = Model::Named('Job_Running');
	$lockfile = appendPath( $workingDir, $processorName . ".lock");
	$lock = new Lock($lockfile);
	if (($pid = $lock->lock()) !== false) {
		if ( is_null($job_id) ) {
			$jobList = $job_run_model->allForProcessorGUID($processorName, $guid );
			if ( is_array($jobList) == false || count($jobList) == 0) {
				$jobRunning = $job_run_model->createForProcessor($processorName, $guid, $pid);
				Logger::logError('jobRunning ' .  var_export($jobRunning, true) );

				try {
					$processor = Processor::Named( $processorName, $guid );
					$processor->processData();
				}
				catch (Exception $e) {
					Logger::logError('Exception ' . $e->getMessage() . ' ' . $e->getTraceAsString() );
				}
				if ( $jobRunning instanceof model\Job_RunningDBO ) {
					$job_run_model->deleteObject($jobRunning);
				}
			}
			else {
				Logger::logError('Jobs running for ' . $guid . ' are ' . var_export($jobList, true));
			}
		}
		else {
			Logger::logInfo('Running daemon for specific job ' . $job_id);
			$job_model = Model::Named('Job');
			$jobObj = $job_model->objectForId( $job_id );
			if ( $jobObj instanceof model\JobDBO ) {
				$jobList = $job_run_model->allForJob($jobObj );
				if ( is_array($jobList) == false || count($jobList) == 0) {
					$jobRunning = $job_run_model->create($jobObj, $jobObj->jobType(), $processorName, $guid, $pid);
					try {
						$processor = Processor::Named( $processorName, $guid );

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
						$processor->processData();
						// success, calc next job schedule run
						$jobObj->{"last_run"}(time());
					}
					catch (Exception $e) {
						Logger::logException( $e );

						// disable job???
						// count failures, disable on 5th fail?
						$jobObj->{"enabled"}(Model::TERTIARY_FALSE);
					}

					if ( $jobRunning instanceof model\Job_RunningDBO ) {
						$job_run_model->deleteObject($jobRunning);
					}
				}
				else {
					Logger::logError('Jobs running for ' . $jobObj . ' are ' . var_export($jobList, true));
				}
			}
			else {
				Logger::logError('Failed to find Job for ' . $job_id );
			}
		}

		$lock->unlock();
	}
}
catch ( ClassNotFoundException $exception ) {
	Logger::logException( $exception );
	Logger::logError( 'Failed to find processor ' . $options['p'] );
}

if ( is_null($debug) && is_sub_dir($workingDir, $config->processingDirectory()) ) {
	destroy_dir($workingDir);
}

Logger::logInfo( 'finished daemon');

?>
