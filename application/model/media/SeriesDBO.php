<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \db\Qualifier as Qualifier;

use \model\media\Series as Series;

/* import related objects */
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_AliasDBO as Series_AliasDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;

class SeriesDBO extends _SeriesDBO
{
	public function availableSummary()
	{
		return
			(isset($this->pub_available) ? $this->pub_available : 0 )
			. " / "
			. (isset($this->pub_count) ? $this->pub_count : count($this->publications()) );
	}

	public function seriesStartYearString()
	{
		$yearStr = '0000';
		if ( isset($this->start_year) ) {
			if ( is_integer($this->start_year) ) {
				$yearStr = sprintf( '%04d', $this->start_year);
			}
			else if (is_numeric($this->start_year)) {
				$yearStr = sprintf( '%04d', intval($this->start_year));
			}
		}
		return $yearStr;
	}

	public function lastPublication()
	{
		$select = \SQL::Select( Model::Named("Publication") );
		$select->where( Qualifier::FK( Publication::series_id, $this));
		$select->limit( 1 );
		$select->orderBy( array(array(\SQL::SQL_ORDER_DESC => Publication::pub_date)) );
		return $select->fetch();
	}

	public function activePublications()
	{
		$model = Model::Named('Publication');
		return $model->activePublicationsForSeries($this);
	}

	public function publicationForExternal($issue_xid, $xsource)
	{
		$model = Model::Named('Publication');
		return $model->publicationForSeriesExternal($this, $issue_xid, $xsource);
	}

	public function addAlias($name = null)
	{
		if ( isset($name) ) {
			$alias_model = Model::Named('Series_Alias');
			return $alias_model->createAlias($this, $name);
		}
		return false;
	}

	public function characters($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Character") );
		$select->joinOn( Model::Named("Character"), Model::Named("Series_Character"), null,
			Qualifier::FK( Series_Character::series_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Character"), Character::popularity, "desc");
		return $select->fetchAll();
	}

	public function joinToCharacter(\model\media\CharacterDBO $character) {
		$model = Model::Named('Series_Character');
		return $model->createJoin($this, $character);
	}

	public function story_arcs($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Story_Arc") );
		$select->joinOn( Model::Named("Story_Arc"), Model::Named("Story_Arc_Series"), null,
			Qualifier::FK( Story_Arc_Series::series_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Story_Arc"), Story_Arc::name);
		return $select->fetchAll();
	}

	public function joinToStory_Arc(model\Story_ArcDBO $object) {
		$model = Model::Named('Story_Arc_Series');
		return $model->createJoin($object, $this);
	}

	public function userFavorite($userId = null) {
		$join = $this->userjoin($userId);
		if ( $join != false ) {
			return $join->favorite;
		}
		return false;
	}

	public function userJoin($userId = null) {
		if (isset($userId)) {
			$usermodel = Model::Named('Users');
			$model = Model::Named('User_Series');
			$user = $usermodel->objectForId($userId);
			return $model->joinForUserAndSeries($user, $this);
		}
		return false;
	}

	public function notify( $type = 'none', $object = null )
	{
		Logger::logInfo( $this . " Notified $type " . $object );
		if ( $object instanceof DataObject ) {
			switch( $object->tableName() ) {
				case 'media':
					if ( $type === Model::NotifyInserted || $type === Model::NotifyDeleted ) {
						\SQL::raw(
							"update series set pub_available = ( "
								. "select count(*) from publication where publication.series_id = series.id AND publication.media_count > 0 "
								. ") where id = :myid;",
							array( ":myid" => $this->id)
						);
					}
					break;
				case 'publication':
					if ( $type === Model::NotifyInserted || $type === Model::NotifyUpdated || $type === Model::NotifyDeleted ) {
						\SQL::raw(
							"update series set pub_count = ( "
								. "select count(*) from publication where publication.series_id = series.id"
								. ") where id = :myid;",
							array( ":myid" => $this->id)
						);
						\SQL::raw(
							"update series set pub_cycle = ( "
								. "select (julianday(max(pub_date), 'unixepoch') - julianday(min(pub_date), 'unixepoch')) / count(*) "
								. "from publication where publication.series_id = series.id"
								. ") where id = :myid;",
							array( ":myid" => $this->id)
						);
						\SQL::raw(
							"update series set pub_active = ( "
								. "select (((julianday('now') - julianday(max(pub_date), 'unixepoch'))/365) < 1)"
								. "from publication where publication.series_id = series.id"
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

?>
