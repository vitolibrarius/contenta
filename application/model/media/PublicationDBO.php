<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \db\Qualifier as Qualifier;

use \model\media\Publication as Publication;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

class PublicationDBO extends _PublicationDBO
{
	public function publisher()
	{
		$series = $this->series();
		if ( $series != false) {
			return $series->publisher();
		}
		return false;
	}

	public function displayName()
	{
		return $this->series()->name()
			. " [" . $this->paddedIssueNum() . "]"
			. ($this->publishedYear() > 1900 ? " " . $this->publishedYear() : '');
	}

	public function searchString()
	{
		return $this->series()->search_name
			. " " . $this->paddedIssueNum()
			. ($this->publishedYear() > 1900 ? " " . $this->publishedYear() : '');
	}

	public function paddedIssueNum()
	{
		if ( isset($this->issue_num) ) {
			if ( is_numeric($this->issue_num) ) {
				return str_pad($this->issue_num, 3, "0", STR_PAD_LEFT);
			}
			return $this->issue_num;
		}
		return '';
	}

	public function publishedMonthYear()
	{
		return $this->formattedDate( Publication::pub_date, "M Y" );
	}

	public function publishedYear()
	{
		if (isset($this->pub_date) && ($this->pub_date > 0)) {
			$coverDate = getdate($this->pub_date);
			if (isset($coverDate['year'])) {
				return $coverDate['year'];
			}
		}
		return 0;
	}

	public function seriesName()
	{
		$series = $this->series();
		if ( $series != false ) {
			return $series->name();
		}
		return '';
	}

	public function characters($limit = null)
	{
		$select = \SQL::SelectJoin( Model::Named("Character") );
		$select->joinOn( Model::Named("Character"), Model::Named("Publication_Character"), null,
			Qualifier::FK( Publication_Character::publication_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Character"), Character::popularity, "desc" );
		return $select->fetchAll();
	}

	public function joinToCharacter($object)
	{
		$model = Model::Named('Publication_Character');
		return $model->createJoin( $this, $object );
	}

	public function story_arcs($limit = null)
	{
		$select = \SQL::SelectJoin( Model::Named("Story_Arc") );
		$select->joinOn( Model::Named("Story_Arc"), Model::Named("Story_Arc_Publication"), null,
			Qualifier::FK( Story_Arc_Publication::publication_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Story_Arc"), Story_Arc::name );
		return $select->fetchAll();
	}

	public function joinToStory_Arc($object)
	{
		$model = Model::Named('Story_Arc_Publication');
		return $model->createJoin( $object, $this );
	}

	public function rssMatches()
	{
		$model = Model::Named('Rss');
		return $model->objectsForNameIssueYear(
			$this->series()->search_name,
			$this->issue_num,
			($this->publishedYear() > 1900 ? " " . $this->publishedYear() : '')
		);
	}

	public function notify( $type = 'none', $object = null )
	{
// 		Logger::logInfo( $this . " Notified $type " . $object, "Notification", $type );
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

?>
