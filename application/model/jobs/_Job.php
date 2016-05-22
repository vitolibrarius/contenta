<?php

namespace model\jobs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\jobs\JobDBO as JobDBO;

/* import related objects */
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_TypeDBO as Job_TypeDBO;
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

/** Sample Creation script */
		/** JOB */
/*
		$sql = "CREATE TABLE IF NOT EXISTS job ( "
			. Job::id . " INTEGER PRIMARY KEY, "
			. Job::type_id . " INTEGER, "
			. Job::endpoint_id . " INTEGER, "
			. Job::enabled . " INTEGER, "
			. Job::one_shot . " INTEGER, "
			. Job::fail_count . " INTEGER, "
			. Job::elapsed . " INTEGER, "
			. Job::minute . " TEXT, "
			. Job::hour . " TEXT, "
			. Job::dayOfWeek . " TEXT, "
			. Job::parameter . " TEXT, "
			. Job::next . " INTEGER, "
			. Job::last_run . " INTEGER, "
			. Job::last_fail . " INTEGER, "
			. Job::created . " INTEGER, "
			. "FOREIGN KEY (". Job::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "job", $sql, "Create table job" );

*/
abstract class _Job extends Model
{
	const TABLE = 'job';
	const id = 'id';
	const type_id = 'type_id';
	const endpoint_id = 'endpoint_id';
	const enabled = 'enabled';
	const one_shot = 'one_shot';
	const fail_count = 'fail_count';
	const elapsed = 'elapsed';
	const minute = 'minute';
	const hour = 'hour';
	const dayOfWeek = 'dayOfWeek';
	const parameter = 'parameter';
	const next = 'next';
	const last_run = 'last_run';
	const last_fail = 'last_fail';
	const created = 'created';

	public function tableName() { return Job::TABLE; }
	public function tablePK() { return Job::id; }

	public function sortOrder()
	{
		return array(
			array( 'desc' => Job::next)
		);
	}

	public function allColumnNames()
	{
		return array(
			Job::id,
			Job::type_id,
			Job::endpoint_id,
			Job::enabled,
			Job::one_shot,
			Job::fail_count,
			Job::elapsed,
			Job::minute,
			Job::hour,
			Job::dayOfWeek,
			Job::parameter,
			Job::next,
			Job::last_run,
			Job::last_fail,
			Job::created
		);
	}

	/**
	 *	Simple fetches
	 */
	public function allForMinute($value)
	{
		return $this->allObjectsForKeyValue(Job::minute, $value);
	}

	public function allForHour($value)
	{
		return $this->allObjectsForKeyValue(Job::hour, $value);
	}

	public function allForDayOfWeek($value)
	{
		return $this->allObjectsForKeyValue(Job::dayOfWeek, $value);
	}

	public function allForParameter($value)
	{
		return $this->allObjectsForKeyValue(Job::parameter, $value);
	}


	public function allForEndpoint($obj)
	{
		return $this->allObjectsForFK(Job::endpoint_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "job_type":
					return array( Job::job_type_id, "id"  );
					break;
				case "endpoint":
					return array( Job::endpoint_id, "id"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	/**
	 *	Create/Update functions
	 */
	public function base_create( $endpoint, $type_id, $enabled, $one_shot, $fail_count, $elapsed, $minute, $hour, $dayOfWeek, $parameter, $next, $last_run, $last_fail)
	{
		$obj = false;
		if ( isset($endpoint, $minute, $hour, $dayOfWeek, $next) ) {
			$params = array(
				Job::type_id => (isset($type_id) ? $type_id : null),
				Job::enabled => (isset($enabled) ? $enabled : Model::TERTIARY_TRUE),
				Job::one_shot => (isset($one_shot) ? $one_shot : Model::TERTIARY_TRUE),
				Job::fail_count => (isset($fail_count) ? $fail_count : null),
				Job::elapsed => (isset($elapsed) ? $elapsed : null),
				Job::minute => (isset($minute) ? $minute : null),
				Job::hour => (isset($hour) ? $hour : null),
				Job::dayOfWeek => (isset($dayOfWeek) ? $dayOfWeek : null),
				Job::parameter => (isset($parameter) ? $parameter : null),
				Job::next => (isset($next) ? $next : time()),
				Job::last_run => (isset($last_run) ? $last_run : time()),
				Job::last_fail => (isset($last_fail) ? $last_fail : time()),
				Job::created => time(),
			);

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$params[Job::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$params[Job::endpoint_id] = $endpoint;
				}
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( JobDBO $obj,
		$endpoint, $type_id, $enabled, $one_shot, $fail_count, $elapsed, $minute, $hour, $dayOfWeek, $parameter, $next, $last_run, $last_fail)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($type_id) && (isset($obj->type_id) == false || $type_id != $obj->type_id)) {
				$updates[Job::type_id] = $type_id;
			}
			if (isset($enabled) && (isset($obj->enabled) == false || $enabled != $obj->enabled)) {
				$updates[Job::enabled] = $enabled;
			}
			if (isset($one_shot) && (isset($obj->one_shot) == false || $one_shot != $obj->one_shot)) {
				$updates[Job::one_shot] = $one_shot;
			}
			if (isset($fail_count) && (isset($obj->fail_count) == false || $fail_count != $obj->fail_count)) {
				$updates[Job::fail_count] = $fail_count;
			}
			if (isset($elapsed) && (isset($obj->elapsed) == false || $elapsed != $obj->elapsed)) {
				$updates[Job::elapsed] = $elapsed;
			}
			if (isset($minute) && (isset($obj->minute) == false || $minute != $obj->minute)) {
				$updates[Job::minute] = $minute;
			}
			if (isset($hour) && (isset($obj->hour) == false || $hour != $obj->hour)) {
				$updates[Job::hour] = $hour;
			}
			if (isset($dayOfWeek) && (isset($obj->dayOfWeek) == false || $dayOfWeek != $obj->dayOfWeek)) {
				$updates[Job::dayOfWeek] = $dayOfWeek;
			}
			if (isset($parameter) && (isset($obj->parameter) == false || $parameter != $obj->parameter)) {
				$updates[Job::parameter] = $parameter;
			}
			if (isset($next) && (isset($obj->next) == false || $next != $obj->next)) {
				$updates[Job::next] = $next;
			}
			if (isset($last_run) && (isset($obj->last_run) == false || $last_run != $obj->last_run)) {
				$updates[Job::last_run] = $last_run;
			}
			if (isset($last_fail) && (isset($obj->last_fail) == false || $last_fail != $obj->last_fail)) {
				$updates[Job::last_fail] = $last_fail;
			}

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$updates[Job::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$updates[Job::endpoint_id] = $endpoint;
				}
			}

			if ( count($updates) > 0 ) {
				list($obj, $errorList) = $this->updateObject( $obj, $updates );
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Job )
		{
			// does not own Job_Type
			// does not own Endpoint
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForEndpoint(EndpointDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForEndpoint($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */
	public function messagesSince( $sessionId, $lastCheck )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		if ( isset($sessionId)) {
			$qualifiers[] = Qualifier::Equals( 'session', $sessionId);
		}
		if ( isset($lastCheck)) {
			$qualifiers[] = Qualifier::GreaterThan( 'created', $lastCheck);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}

	public function mostRecentLike( $trace, $trace_id, $context, $context_id, $levelCode, $message )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		if ( isset($trace)) {
			$qualifiers[] = Qualifier::Like( 'trace', $trace, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($trace_id)) {
			$qualifiers[] = Qualifier::Like( 'trace_id', $trace_id, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($context)) {
			$qualifiers[] = Qualifier::Like( 'context', $context, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($context_id)) {
			$qualifiers[] = Qualifier::Like( 'context_id', $context_id, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($message)) {
			$qualifiers[] = Qualifier::Like( 'message', $message, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($levelCode)) {
			$qualifiers[] = Qualifier::Equals( 'level_code', $levelCode);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}

}

?>
