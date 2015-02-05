<?php

namespace model;

use \Database as Database;
use \DataObject as DataObject;
use \Model as Model;
use model\Log_Level as Log_Level;

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

	public function __construct(Database $db)
	{
		parent::__construct($db);
	}

	public function tableName() { return Log::TABLE; }
	public function tablePK() { return Log::id; }
	public function sortOrder() { return array("desc" => array(Log::id)); }

	public function dboClassName() { return 'model\\LogDBO'; }

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

		$newObjId = $this->createObj(Log::TABLE, array(
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
		$likes = array();
		if ( isset($trace) && strlen($trace) > 0 ) {
			$likes[Log::trace] = $trace;
		}
		if ( isset($trace_id) && strlen($trace_id) > 0 ) {
			$likes[Log::trace_id] = $trace_id;
		}
		if ( isset($context) && strlen($context) > 0 ) {
			$likes[Log::context] = $context;
		}
		if ( isset($context_id) && strlen($context_id) > 0 ) {
			$likes[Log::context_id] = $context_id;
		}
		if ( isset($level) && strlen($level) > 0 && $level != "any") {
			$likes[Log::level] = $level;
		}
		if ( isset($message) && strlen($message) > 0 ) {
			$likes[Log::message] = $message;
		}

		if ( $direction != 'desc' && $direction != 'asc') {
			$direction = 'desc';
		}

		return $this->fetchAllLike(Log::TABLE, $this->allColumns(), $likes, null, array($direction => array(Log::id)), $limit, 'OR');
	}
}
?>
