<?php

namespace model\reading;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;

use \db\Qualifier as Qualifier;

use \model\reading\Reading_QueueDBO as Reading_QueueDBO;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

class Reading_Queue extends _Reading_Queue
{
	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			if ( isset($values[Reading_Queue::queue_order]) == false ) {
				$user_id = 0;
				if ( isset($values[Reading_Queue::user_id] )) {
					$user_id = $values[Reading_Queue::user_id];
				}
				else if ( isset( $values[Reading_Queue::user] ) && $values[Reading_Queue::user] instanceof UsersDBO) {
					$user_id = $values[Reading_Queue::user]->pkValue();
				}

				if ( $user_id > 0 ) {
					$select = SQL::Maximum( $this,
						Reading_Queue::queue_order,
						null,
						Qualifier::Equals( Reading_Queue::user_id, $user_id ),
						null
					);

					$result = $select->fetch();
					$idx = ( isset($result, $result->max) ? $result->max : 1000 );
					$values[Reading_Queue::queue_order] = $idx + 10;
				}
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Reading_QueueDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Reading_Queue::user_id,
			Reading_Queue::series_id,
			Reading_Queue::story_arc_id,
			Reading_Queue::created,
			Reading_Queue::title,
			Reading_Queue::favorite,
			Reading_Queue::pub_count,
			Reading_Queue::pub_read,
			Reading_Queue::queue_order
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	/*
	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}
	*/

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( Reading_Queue::user_id == $attr ) {
			$model = Model::Named('Users');
			return $model->allObjects();
		}
		if ( Reading_Queue::series_id == $attr ) {
			$model = Model::Named('Series');
			return $model->allObjects();
		}
		if ( Reading_Queue::story_arc_id == $attr ) {
			$model = Model::Named('Story_Arc');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_user_id($object = null, $value)
	{
		return parent::validate_user_id($object, $value);
	}
*/

/*
	function validate_series_id($object = null, $value)
	{
		return parent::validate_series_id($object, $value);
	}
*/

/*
	function validate_story_arc_id($object = null, $value)
	{
		return parent::validate_story_arc_id($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}
*/

/*
	function validate_title($object = null, $value)
	{
		return parent::validate_title($object, $value);
	}
*/

/*
	function validate_favorite($object = null, $value)
	{
		return parent::validate_favorite($object, $value);
	}
*/

/*
	function validate_pub_count($object = null, $value)
	{
		return parent::validate_pub_count($object, $value);
	}
*/

/*
	function validate_queue_order($object = null, $value)
	{
		return parent::validate_queue_order($object, $value);
	}
*/

	public function createReadingQueueSeries( UsersDBO $user, SeriesDBO $series )
	{
		if ( is_null($user) == false && is_null($series) == false ) {
			$readingQueue = $this->objectForUserAndSeries($user, $series);
			if ( $readingQueue == false ) {
				list($readingQueue, $errors) = $this->createObject( array(
					Reading_Queue::user => $user,
					Reading_Queue::series => $series,
					Reading_Queue::title => $series->displayName(),
					Reading_Queue::pub_count => $series->pub_available()
					)
				);
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Reading Queue " . var_export($errors, true) );
				}
			}
			return $readingQueue;
		}
		return false;
	}

	public function createReadingQueueStoryArc( UsersDBO $user, Story_ArcDBO $story )
	{
		if ( is_null($user) == false && is_null($story) == false ) {
			$readingQueue = $this->objectForUserAndStoryArc($user, $story);
			if ( $readingQueue == false ) {
				list($readingQueue, $errors) = $this->createObject( array(
					Reading_Queue::user => $user,
					Reading_Queue::story_arc => $story,
					Reading_Queue::title => $story->displayName(),
					Reading_Queue::pub_count => $story->pub_available()
					)
				);
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Reading Queue " . var_export($errors, true) );
				}
			}
			return $readingQueue;
		}
		return false;
	}

	public function allForUserUnread( UsersDBO $user, $unreadOnly = true, $limit = 50 )
	{
		if ( $user != null ) {
			if ( $unreadOnly == true ) {
				$select = \SQL::Select($this);
				$select->where(
					Qualifier::AndQualifier(
						Qualifier::FK( Reading_Queue::user_id, $user ),
						Qualifier::AttributeCompare(
							Reading_Queue::pub_count,
							Qualifier::GREATER_THAN,
							Reading_Queue::pub_read
						)
					)
				);
				$select->orderBy( $this->sortOrder() );
				$select->limit($limit);
				return $select->fetchAll();
			}
			else {
				return $this->allForUser($user);
			}
		}
		return false;
	}

	public function moveToTopQueuePriority( Reading_QueueDBO $queue = null )
	{
		if ( $queue != null ) {
			// the queue shouldn't ever have a value less than 0, so setting this one to -1 makes it the top, then reset the
			// queue order makes everything positive again
			$queue->setQueue_order(-1);
			$queue->saveChanges();
			$this->resetQueuePriority($queue->user());
		}
	}

	public function resetQueuePriority( UsersDBO $user = null )
	{
		$user_id_filter = "";
		$temp_table = "que_reorder";
		$params = array();
		if ( $user != null ) {
			$user_id = $user->pkValue();
			$temp_table = "que_reorder_".$user_id;
			$user_id_filter = "where user_id = :uid";
			$params[":uid"] = $user_id;
		}

		// create temp table queorder as select id as qid, queue_order, title from reading_queue order by queue_order, title;
		SQL::raw( "create temp table ".$temp_table." as select id as qid, user_id, queue_order, title"
			. " from reading_queue ".$user_id_filter." order by queue_order, title", $params );

		// update reading_queue set queue_order = (select (rowid) from queorder where queorder.qid = reading_queue.id);
		SQL::raw( "update reading_queue set queue_order = (select (rowid * 10) from ".$temp_table." x where x.qid = reading_queue.id)");

	}

	public function increaseQueuePriority( Reading_QueueDBO $queue )
	{
		if ( $queue != null ) {
			//Maximum( Model $model, $target, array $columns = null, db\Qualifier $qualifier = null, array $order = null )
			$select = SQL::Maximum( $this,
				Reading_Queue::queue_order,
				null,
				Qualifier::AndQualifier(
					Qualifier::Equals( Reading_Queue::user_id, $queue->user_id ),
					Qualifier::LessThan( Reading_Queue::queue_order, $queue->queue_order )
				),
				null
			);
			$result = $select->fetch();
			$idx = ( isset($result, $result->max) ? $result->max : $queue->queue_order );
			if ( $idx < $queue->queue_order ) {
				// fetch all for that order
				$update = SQL::Update( $this,
					Qualifier::AndQualifier(
						Qualifier::Equals( Reading_Queue::user_id, $queue->user_id ),
						Qualifier::Equals( Reading_Queue::queue_order, $idx )
					),
					array( Reading_Queue::queue_order => $queue->queue_order )
				);
				$update->commitTransaction();

				$queue->setQueue_order($idx);
				$queue->saveChanges();
			}
		}
	}

	public function decreaseQueuePriority( Reading_QueueDBO $queue )
	{
		if ( $queue != null ) {
			//Minimum( Model $model, $target, array $columns = null, db\Qualifier $qualifier = null, array $order = null )
			$select = SQL::Minimum( $this,
				Reading_Queue::queue_order,
				null,
				Qualifier::AndQualifier(
					Qualifier::Equals( Reading_Queue::user_id, $queue->user_id ),
					Qualifier::GreaterThan( Reading_Queue::queue_order, $queue->queue_order )
				),
				null
			);
			$result = $select->fetch();
			$idx = ( isset($result, $result->min) ? $result->min : $queue->queue_order );
			if ( $idx > $queue->queue_order ) {
				// fetch all for that order
				$update = SQL::Update( $this,
					Qualifier::AndQualifier(
						Qualifier::Equals( Reading_Queue::user_id, $queue->user_id ),
						Qualifier::Equals( Reading_Queue::queue_order, $idx )
					),
					array( Reading_Queue::queue_order => $queue->queue_order )
				);
				$update->commitTransaction();

				$queue->setQueue_order($idx);
				$queue->saveChanges();
			}
		}
	}
}

?>
