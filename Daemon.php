<?php

	$system_path = dirname(__FILE__);
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	define('SYSTEM_PATH', str_replace("\\", "/", $system_path));
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

    $options = getopt("g:u:p:");

	printf('starting daemon ' . $options['p'] . ' for id ' . $options['g'] . PHP_EOL);
	$trace = uuid();
	$traceid = $options['p'] . '-' . basename($options['g']);
	Logger::instance()->setTrace( $trace, $traceid );

	try {
		$processor = Processor::Named( $options['p'], $options['g'] );
		$lockfile = appendPath( sys_get_temp_dir(), $traceid . ".lock");
		printf('    Lock ' . $lockfile . PHP_EOL);
		$lock = new Lock($lockfile);

		if (($pid = $lock->lock()) !== false) {
			try
			{
				$processor->processData();

// 				$log_model = loadModel('Log');
// 				$array = $log_model->fetchAll(LogModel::TABLE, $log_model->allColumns(),
// 					array(LogModel::trace => $trace, LogModel::trace_id => $traceid),
// 					array(LogModel::created)
// 				);

// 				if ( is_array($array) ) {
// 					foreach ($array as $key => $log) {
// 						printf( $log->description() . PHP_EOL);
// 					}
// 				}
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
