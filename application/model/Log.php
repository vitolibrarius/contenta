<?php

namespace model;

use \Database as Database;
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\Log_Level as Log_Level;

use \SQL as SQL;
use db\Qualifier as Qualifier;

class Log extends Model
{
	const TABLE =		'log';
	const id =			'id';
	const trace =		'trace';
	const trace_id =	'trace_id';
	const context =		'context';
	const context_id =	'context_id';
	const level =		'level';
	const created =	'created';
	const message =	'message';


	public function tableName() { return Log::TABLE; }
	public function tablePK() { return Log::id; }
	public function sortOrder() { return array(
			array("desc" => Log::id)
		);
	}

	public function allColumnNames()
	{
		return array(
			Log::id, Log::trace, Log::trace_id, Log::context, Log::context_id, Log::level, Log::created, Log::message
		);
	}

	function create( $level, $message, $traceName = null, $traceId = null, $contextName = null, $contextId = null)
	{
		$log_level_model = Model::Named("Log_Level");
		$levelObj = $log_level_model->logLevelForCode($level);

		$this->createObject(array(
			Log::created => time(),
			Log::trace => $traceName,
			Log::trace_id => $traceId,
			Log::context => $contextName,
			Log::context_id => $contextId,
			Log::level => ($levelObj == false ? 'warning' : $levelObj->{Log_Level::code}),
			Log::message => $message
			)
		);
		return true;
	}

	public function mostRecentLike( $trace = null, $trace_id = null, $context = null, $context_id = null, $level = null, $message = null, $direction="desc", $limit=50) {
		$select = \SQL::Select( $this )->limit( 50 );

		$likes = array();
		if ( isset($trace) && strlen($trace) > 0 ) {
			$likes[] = Qualifier::Like( Log::trace, $trace, SQL::SQL_LIKE_AFTER );
		}
		if ( isset($trace_id) && strlen($trace_id) > 0 ) {
			$likes[] = Qualifier::Like( Log::trace_id, $trace_id, SQL::SQL_LIKE_AFTER );
		}
		if ( isset($context) && strlen($context) > 0 ) {
			$likes[] = Qualifier::Like( Log::context, $context, SQL::SQL_LIKE_AFTER );
		}
		if ( isset($context_id) && strlen($context_id) > 0 ) {
			$likes[] = Qualifier::Like( Log::context_id, $context_id, SQL::SQL_LIKE_AFTER );
		}
		if ( isset($level) && strlen($level) > 0 && $level != "any") {
			$likes[] = Qualifier::Like( Log::level, $level, SQL::SQL_LIKE_AFTER );
		}
		if ( isset($message) && strlen($message) > 0 ) {
			$likes[] = Qualifier::Like( Log::message, $message, SQL::SQL_LIKE_AFTER );
		}

		if ( count($likes) > 0 ) {
			$select->where( Qualifier::AndQualifier( $likes ));
		}

		if ( $direction != 'desc' && $direction != 'asc') {
			$direction = 'desc';
		}
		$select->orderBy( array( array($direction => Log::id) ) );

 		return $select->fetchAll();
	}
}
?>
