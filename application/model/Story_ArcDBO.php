<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;
use \Logger as Logger;

use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;

use db\Qualifier as Qualifier;

class Story_ArcDBO extends DataObject
{
	public $name;
	public $created;
	public $desc;
	public $xurl;
	public $xsource;
	public $xid;
	public $xupdated;
	public $publisher_id;
	public $pub_active;
	public $pub_cycle;
	public $pub_count;
	public $pub_available;
	public $pub_wanted;

	public function displayName() {
		return $this->name;
	}

	public function isActive() {
		return (isset($this->pub_active) && $this->pub_active == Model::TERTIARY_TRUE);
	}

	public function isWanted() {
		return (isset($this->pub_wanted) && $this->pub_wanted == Model::TERTIARY_TRUE);
	}

	public function availableSummary() {
		return
			(isset($this->pub_available) ? $this->pub_available : 0 )
			. " / "
			. (isset($this->pub_count) ? $this->pub_count : count($this->publications()) );
	}

	public function publisher() {
		if ( isset($this->publisher_id) ) {
			$model = Model::Named('Publisher');
			return $model->objectForId($this->publisher_id);
		}
		return false;
	}

	public function setPublisher( model\PublisherDBO $pubObj )
	{
		if ( isset($pubObj, $pubObj->id) && (isset($this->publisher_id) == false || $pubObj->id != $this->publisher_id) ) {
			$updates = array();
			$updates[Character::publisher_id] = $pubObj->id;
			$this->model()->updateObject($this, $updates );
		}
	}

	public function characters($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Character") );
		$select->joinOn( Model::Named("Character"), Model::Named("Story_Arc_Character"), null,
			Qualifier::FK( Story_Arc_Character::story_arc_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Character"), Character::popularity, "desc");
		return $select->fetchAll();
	}

	public function joinToCharacter(model\CharacterDBO $character) {
		$model = Model::Named('Story_Arc_Character');
		return $model->create($this, $character);
	}

	public function series($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Series") );
		$select->joinOn( Model::Named("Series"), Model::Named("Story_Arc_Series"), null,
			Qualifier::FK( Story_Arc_Series::story_arc_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Series"), Series::popularity, "desc");
		return $select->fetchAll();
	}

	public function joinToSeries(model\SeriesDBO $series) {
		$model = Model::Named('Story_Arc_Series');
		return $model->create($this, $series);
	}

	public function publications($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Publication") );
		$select->joinOn( Model::Named("Publication"), Model::Named("Story_Arc_Publication"), null,
			Qualifier::FK( Story_Arc_Publication::story_arc_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Publication"), Publication::name);
		return $select->fetchAll();
	}

	public function joinToPublication(model\PublicationDBO $publication) {
		$model = Model::Named('Story_Arc_Publication');
		return $model->create($this, $publication);
	}

	public function notify( $type = 'none', $object = null )
	{
// 		Logger::logInfo( $this . " Notified $type " . $object );
		if ( $object instanceof DataObject ) {
			switch( $object->tableName() ) {
				case 'media':
					if ( $type === Model::NotifyInserted || $type === Model::NotifyDeleted ) {
						\SQL::raw(
							"update story_arc set pub_available = ( "
								. "select count(*) from story_arc_publication join publication on "
								. "story_arc_publication.publication_id = publication.id "
								. "where story_arc_publication.story_arc_id = story_arc.id AND publication.media_count > 0"
								. ") where id = :myid;",
							array( ":myid" => $this->id)
						);
					}
					break;
				case 'publication':
				case 'story_arc_publication':
					if ( $type === Model::NotifyInserted || $type === Model::NotifyUpdated || $type === Model::NotifyDeleted ) {
						\SQL::raw(
							"update story_arc set pub_count = ( "
								. "select count(*) from story_arc_publication join publication on "
								. "story_arc_publication.publication_id = publication.id "
								. "where story_arc_publication.story_arc_id = story_arc.id"
								. ") where id = :myid;",
							array( ":myid" => $this->id)
						);
						\SQL::raw(
							"update story_arc set pub_cycle = ( "
								. "select (julianday(max(publication.pub_date), 'unixepoch') - julianday(min(publication.pub_date), 'unixepoch')) / count(*) "
								. "from story_arc_publication join publication on story_arc_publication.publication_id = publication.id "
								. "where story_arc_publication.story_arc_id = story_arc.id"
								. ") where id = :myid;",
							array( ":myid" => $this->id)
						);
						\SQL::raw(
							"update story_arc set pub_active = ( "
								. "select (((julianday('now') - julianday(max(pub_date), 'unixepoch'))/365) < 1) "
								. "from story_arc_publication join publication on story_arc_publication.publication_id = publication.id "
								. "where story_arc_publication.story_arc_id = story_arc.id"
								. ") where id = :myid;",
							array( ":myid" => $this->id)
						);
					}
					break;
				default:
					Logger::logError( $this . " Notified about unknown value " . $object );
					break;
			}
		}
	}
}

