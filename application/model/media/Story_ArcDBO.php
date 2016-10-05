<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \db\Qualifier as Qualifier;

use \http\Session as Session;
use \model\media\Story_Arc as Story_Arc;

/* import related objects */
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_CharacterDBO as Story_Arc_CharacterDBO;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;

class Story_ArcDBO extends _Story_ArcDBO
{
	public function availableSummary()
	{
		return
			(isset($this->pub_available) ? $this->pub_available : 0 )
			. " / "
			. (isset($this->pub_count) ? $this->pub_count : count($this->publications()) );
	}

	public function queued()
	{
		$user = Session::sessionUser();
		if ( $user != false ) {
			$queue = Model::Named('Reading_Queue')->objectForUserAndStoryArc($user, $this);
			return ( $queue != false );
		}
		return false;
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

	public function joinToCharacter(CharacterDBO $character) {
		$model = Model::Named('Story_Arc_Character');
		return $model->createJoin($this, $character);
	}

	public function series($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Series") );
		$select->joinOn( Model::Named("Series"), Model::Named("Story_Arc_Series"), null,
			Qualifier::FK( Story_Arc_Series::story_arc_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Series"), Series::start_year, "desc");
		return $select->fetchAll();
	}

	public function joinToSeries(SeriesDBO $series)
	{
		$model = Model::Named('Story_Arc_Series');
		return $model->createJoin($this, $series);
	}

	public function publications($limit = null)
	{
		$select = \SQL::SelectJoin( Model::Named("Publication") );
		$select->joinOn( Model::Named("Publication"), Model::Named("Story_Arc_Publication"), null,
			Qualifier::FK( Story_Arc_Publication::story_arc_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Publication"), Publication::pub_date);
		$select->orderBy( Model::Named("Publication"), Publication::series_id);
		$select->orderBy( Model::Named("Publication"), Publication::issue_num);
		return $select->fetchAll();
	}

	public function lastPublication()
	{
		$select = \SQL::SelectJoin( Model::Named("Publication") );
		$select->joinOn( Model::Named("Publication"), Model::Named("Story_Arc_Publication"), null,
			Qualifier::FK( Story_Arc_Publication::story_arc_id, $this)
		);
		$select->limit( 1 );
		$select->orderBy( Model::Named("Publication"), Publication::pub_date);
		return $select->fetch();
	}

	public function joinToPublication(PublicationDBO $publication)
	{
		$model = Model::Named('Story_Arc_Publication');
		return $model->createJoin($this, $publication);
	}

	public function notify( $type = 'none', $object = null )
	{
		Logger::logInfo( $this . " Notified $type " . $object, "Notification", $type );
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

?>
