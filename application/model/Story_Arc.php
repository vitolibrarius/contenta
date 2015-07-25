<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

class Story_Arc extends Model
{
	const TABLE =		'story_arc';
	const id =			'id';
	const created =		'created';
	const name =		'name';
	const desc =		'desc';
	const publisher_id ='publisher_id';
	const xurl =		'xurl';
	const xsource =		'xsource';
	const xid =			'xid';
	const xupdated =	'xupdated';

	const pub_active =		'pub_active';
	const pub_cycle =		'pub_cycle';
	const pub_count =		'pub_count';
	const pub_available = 	'pub_available';
	const pub_wanted =		'pub_wanted';

	public function tableName() { return Story_Arc::TABLE; }
	public function tablePK() { return Story_Arc::id; }
	public function sortOrder() { return array(Story_Arc::name); }

	public function allColumnNames()
	{
		return array(
			Story_Arc::id, Story_Arc::name, Story_Arc::desc, Story_Arc::publisher_id, Story_Arc::created,
			Story_Arc::xurl, Story_Arc::xsource, Story_Arc::xid, Story_Arc::xupdated,
			Story_Arc::pub_active, Story_Arc::pub_cycle, Story_Arc::pub_count, Story_Arc::pub_available, Story_Arc::pub_wanted
		);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "story_arc_character":
				case "story_arc_publication":
				case "story_arc_series":
					return array( Story_Arc::id, "story_arc_id" );
					break;
				case "publisher":
					return array( Story_Arc::publisher_id, "id" );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	public function findExternalOrCreate( $publisher = null, $name, $desc, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false ) {
				$obj = $this->create($publisher, $name, $desc, $xid, $xsrc, $xurl);
			}
			else {
				$updates = array();

				if ( isset($publisher, $publisher->id) && (isset($obj->publisher_id) == false || $publisher->id != $obj->publisher_id) ) {
					$updates[Story_Arc::publisher_id] = $publisher->id;
				}

				if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
					$updates[Story_Arc::name] = $name;
				}

				if (isset($desc) && strlen($desc) > 0) {
					$desc = strip_tags($desc);
					if ( $desc != $obj->desc ) {
						$updates[Story_Arc::desc] = $desc;
					}
				}

				if ( isset($xid) ) {
					$updates[Story_Arc::xupdated] = time();

					if ((isset($xurl) && strlen($xurl) > 0) && (isset($obj->xurl) == false || strlen($obj->xurl) == 0)) {
						$updates[Story_Arc::xurl] = $xurl;
					}
				}

				if ( count($updates) > 0 ) {
					$this->updateObject($obj, $updates );
				}
			}

			return $obj;
		}
		return false;
	}

	public function create( $publisher = null, $name, $desc, $xid, $xsrc, $xurl = null )
	{
		$obj = $this->objectForExternal($xid, $xsrc);
		if ( $obj == false )
		{
			$params = array(
				Story_Arc::created => time(),
				Story_Arc::name => $name,
				Story_Arc::desc => strip_tags($desc),
				Story_Arc::xurl => $xurl,
				Story_Arc::xsource => $xsrc,
				Story_Arc::xid => $xid,
				Story_Arc::pub_active => Model::TERTIARY_TRUE,
				Story_Arc::pub_cycle => 0,
				Story_Arc::pub_count => 0,
				Story_Arc::pub_available => 0,
				Story_Arc::pub_wanted => Model::TERTIARY_FALSE
			);

			if ( isset($publisher)  && is_a($publisher, '\model\PublisherDBO')) {
				$params[Story_Arc::publisher_id] = $publisher->id;
			}

			$objectOrErrors = $this->createObject($params);
			if ( is_array($objectOrErrors) ) {
				return $objectOrErrors;
			}
			else if ($objectOrErrors != false) {
				$obj = $this->objectForId( (string)$objectOrErrors);
			}
		}

		return $obj;
	}
}
?>
