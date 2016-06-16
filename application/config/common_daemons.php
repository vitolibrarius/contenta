<?php


/**
* this will walk a keypath down a nested associative array and return the leaf value.
*/
function Daemonize( $processorName, $user_api = null, $guid = null, $job_id = null, array $other = array() )
{
	$root = Config::GetProcessing();
	$processingRoot = appendPath($root, "Daemons" );
	makeRequiredDirectory($processingRoot, 'processing subdirectory for Daemons' );

	$workingDir = makeUniqueDirectory($processingRoot, $processorName );
	$metadata = Metadata::forDirectory( $workingDir, Metadata::TYPE_JSON );

	$metadata->setMeta( "user_api", $user_api );
	$metadata->setMeta( "processorName", $processorName );
	$metadata->setMeta( "job_id", $job_id );
	$metadata->setMeta( "guid", $guid );

	if ( is_array($other) ) {
		foreach( $other as $keypath => $value ) {
			$metadata->setMeta( $keypath, $value );
		}
	}

	$shell = ((PHP_OS === 'Darwin') ? '' : 'nohup ') . 'php ';
	$daemon = appendPath(SYSTEM_PATH, 'Daemon.php');
	$daemonCMD = $daemon
		. ' -d "' . $workingDir . '" >> "' . $workingDir . '"/daemon.log 2>&1  & echo $!';

	return shell_exec( $shell . $daemonCMD );
}

function DaemonizeJob( \model\jobs\JobDBO $job = null, model\user\UsersDBO $user = null, array $other = array() )
{
	$api_hash = '';
	if ( is_null($user) ) {
		// throw exception()
	}

	if ( is_null($job) ) {
		// throw exception()
	}

	$type = $job->jobType();
	if ( is_null($type) || $type == false ) {
		// throw exception()
	}

	return Daemonize( $type->processor, $api_hash, $job->uuidParameter(), $job->id, $other );
}
