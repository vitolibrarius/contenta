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

	use \Logger as Logger;
	use \Processor as Processor;
	use utilities\Lock as Lock;

	declare(ticks = 1);
	function signal_handler($signo)
	{
		printf('Received signal ' . $signo . PHP_EOL);
		if ($signo == SIGTERM || $signo == SIGHUP || $signo == SIGINT) {
			if ( isset($lock) ) {
				$lock->unlock();
			}

			printf( 'exiting daemon' . PHP_EOL);
			exit();
		}
	}

	if ( function_exists('pcntl_signal')) {
		// These define the signal handling
		pcntl_signal(SIGTERM, "signal_handler");
		pcntl_signal(SIGHUP,  "signal_handler");
		pcntl_signal(SIGINT, "signal_handler");
	}

	/**
		-g <guid>
		-u <user api hash>
		-p <processor name>
		-j <jobid>
		-t <jobtype>
	 */
    $options = getopt("g:u:p:t:j::");
    $guid = (isset($options['g']) ? $options['g'] : null);
    $user_api = (isset($options['u']) ? $options['u'] : null);
    $processorName = (isset($options['p']) ? $options['p'] : null);
    $job_id = (isset($options['j']) ? $options['j'] : null);
    $jobtype_code = (isset($options['t']) ? $options['t'] : null);

	printf('starting daemon ' . var_export($options, true) . PHP_EOL);
	Logger::instance()->setTrace($processorName, $guid );

	try {
		$lockfile = appendPath( sys_get_temp_dir(), $processorName . '-' . $guid . ".lock");
		printf('    Lock ' . $lockfile . PHP_EOL);
		$lock = new Lock($lockfile);
		if (($pid = $lock->lock()) !== false) {
			$job_run_model = Model::Named('Job_Running');
			$jobList = $job_run_model->allForProcessorGUID($processorName, $guid );
			if ( is_array($jobList) == false || count($jobList) == 0) {
				$jobRunning = $job_run_model->createForJob($job_id, $jobtype_code, $processorName, $guid, $pid);
			}

			try
			{
				$processor = Processor::Named( $processorName, $guid );
				$processor->processData();
			}
			catch (Exception $e)
			{
				printf('Exception ' . $e->getMessage() . ' ' . $e->getTraceAsString() . PHP_EOL);
			}

			$lock->unlock();
		}
		else {
			printf('Processor locked by pid ' . file_get_contents($lockfile) . PHP_EOL);
		}
	}
	catch ( ClassNotFoundException $exception ) {
		Logger::logException( $exception );
		printf('Failed to find processor ' . $options['p'] . PHP_EOL);
	}
	printf( 'finished daemon' . PHP_EOL);
?>
