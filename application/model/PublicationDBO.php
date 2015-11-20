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

class PublicationDBO extends DataObject
{
	public $name;
	public $desc;
	public $pub_date;
	public $created;
	public $issue_num;
	public $series_id;

	public $xurl;
	public $xsource;
	public $xid;
	public $xupdated;
	public $media_count;

	public function displayName() {
		return $this->name;
	}

	public function searchString() {
		return $this->series()->search_name
			. " " . $this->paddedIssueNum()
			. ($this->publishedYear() > 1900 ? " " . $this->publishedYear() : '');
	}

	public function paddedIssueNum() {
		if ( isset($this->issue_num) ) {
			if ( is_numeric($this->issue_num) ) {
				return str_pad($this->issue_num, 3, "0", STR_PAD_LEFT);
			}
			return $this->issue_num;
		}
		return '';
	}

	public function mediaCount() {
		return (isset($this->media_count) ? $this->media_count : 0);
	}

	public function publishedMonthYear() {
		return $this->formattedDate( Publication::pub_date, "M Y" );
	}

	public function publishedYear() {
		if (isset($this->pub_date) && ($this->pub_date > 0)) {
			$coverDate = getdate($this->pub_date);
			if (isset($coverDate['year'])) {
				return $coverDate['year'];
			}
		}
		return 0;
	}

	public function publisher() {
		$series = $this->series();
		if ( $series != false) {
			return $series->publisher();
		}
		return false;
	}

	public function series() {
		if ( isset($this->series_id) ) {
			$model = Model::Named('Series');
			return $model->objectForId($this->series_id);
		}
		return false;
	}

	public function seriesName() {
		if ( isset($this->series_id) ) {
			return $this->series()->name;
		}
		return '';
	}

	public function setSeries( model\SeriesDBO $series ) {
		if ( isset($series, $series->id) && (isset($this->series_id) == false || $series->id != $this->series_id) ) {
			$updates = array();
			$updates[Publication::series_id] = $series->id;
			$this->model()->updateObject($this, $updates );
		}
	}

	public function media() {
		$model = Model::Named('Media');
		return $model->allForPublication($this);
	}

	public function characters($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Character") );
		$select->joinOn( Model::Named("Character"), Model::Named("Publication_Character"), null,
			Qualifier::FK( Publication_Character::publication_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Character"), Character::popularity, "desc" );
		return $select->fetchAll();
	}

	public function joinToCharacter($character) {
		$model = Model::Named('Publication_Character');
		return $model->create($this, $character);
	}

	public function story_arcs($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Story_Arc") );
		$select->joinOn( Model::Named("Story_Arc"), Model::Named("Story_Arc_Publication"), null,
			Qualifier::FK( Story_Arc_Publication::publication_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Story_Arc"), Story_Arc::name );
		return $select->fetchAll();
	}

	public function joinToStory_Arc($object) {
		$model = Model::Named('Story_Arc_Publication');
		return $model->create($object, $this);
	}

	public function notify( $type = 'none', $object = null )
	{
// 		Logger::logInfo( $this . " Notified $type " . $object );
		if ( $object instanceof DataObject ) {
			switch( $object->tableName() ) {
				case 'media':
					if ( $type === Model::NotifyInserted || $type === Model::NotifyDeleted ) {
						\SQL::raw(
							"update publication set media_count = ( "
								. "select count(*) from media where media.publication_id = publication.id "
								. ") where id = :myid;",
							array( ":myid" => $this->id)
						);
					}
					break;
				default:
					break;
			}
		}
	}
}
