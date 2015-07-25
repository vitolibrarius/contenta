<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

use utilities\MediaFilename as MediaFilename;
use model\Endpoint as Endpoint;

class RSS extends Model
{
	const TABLE =		'rss';
	const id =			'id';
	const endpoint_id =	'endpoint_id';
	const created =		'created';

	const title =		'title';
	const desc =		'desc';
	const pub_date =	'pub_date';
	const guid =		'guid';

	const clean_name =	'clean_name';
	const clean_issue =	'clean_issue';
	const clean_year =	'clean_year';

	const enclosure_url =		'enclosure_url';
	const enclosure_length =	'enclosure_length';
	const enclosure_mime =		'enclosure_mime';
	const enclosure_hash =		'enclosure_hash';
	const enclosure_password =	'enclosure_password';

	public function tableName() { return RSS::TABLE; }
	public function tablePK() { return RSS::id; }
	public function sortOrder() { return array(RSS::title, RSS::pub_date); }

	public function allColumnNames()
	{
		return array(
			RSS::id, RSS::endpoint_id, RSS::created,
			RSS::title, RSS::desc, RSS::pub_date, RSS::guid, RSS::clean_name, RSS::clean_issue, RSS::clean_year,
			RSS::enclosure_url, RSS::enclosure_length, RSS::enclosure_mime, RSS::enclosure_hash, RSS::enclosure_password
		);
	}

	public function objectForEndpointGUID( EndpointDBO $endpoint = null, $guid )
	{
		return $this->singleObjectForKeyValues( array( RSS::endpoint_id => $endpoint->id, RSS::guid => $guid ) );
	}

	public function create( EndpointDBO $endpoint = null, $title, $desc, $pub_date, $guid, $encl_url = null, $encl_length = 0, $encl_mime = 'application/x-nzb', $encl_hash = null, $encl_password = false )
	{
		if ( isset($title, $guid) && is_null($endpoint) == false ) {
			$mediaFilename = new MediaFilename($title);
			$meta = $mediaFilename->updateFileMetaData(null);

			$params = array(
				RSS::created => time(),
				RSS::title => $title,
				RSS::desc => $desc,
				RSS::pub_date => $pub_date,
				RSS::guid => $guid,
				RSS::clean_name => $meta['name'],
				RSS::clean_issue => (isset($meta['issue']) ? $meta['issue'] : null),
				RSS::clean_year => (isset($meta['year']) ? $meta['year'] : null),
				RSS::enclosure_url => $encl_url,
				RSS::enclosure_length => $encl_length,
				RSS::enclosure_mime => $encl_mime,
				RSS::enclosure_hash => $encl_hash,
				RSS::enclosure_password => ($encl_password) ? 1 : 0,
			);

			if ( isset($endpoint) ) {
				$params[RSS::endpoint_id] = $endpoint->id;
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
			return $obj;
		}

		return false;
	}

	public function update( RSSDBO $obj = null, $title, $desc, $pub_date, $encl_url = null, $encl_length = 0, $encl_mime = 'application/x-nzb', $encl_hash = null, $encl_password = false )
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($title) && (isset($obj->title) == false || $title != $obj->title)) {
				$mediaFilename = new MediaFilename($title);
				$meta = $mediaFilename->updateFileMetaData(null);

				$updates[RSS::title] = $title;
				$updates[RSS::clean_name] = $meta['name'];
				$updates[RSS::clean_issue] = (isset($meta['issue']) ? $meta['issue'] : null);
				$updates[RSS::clean_year] = (isset($meta['year']) ? $meta['year'] : null);
			}

			if (isset($desc) && strlen($desc) > 0) {
				if ( $desc != $obj->desc ) {
					$updates[RSS::desc] = strip_tags($desc);
				}
			}

			if (isset($pub_date) && (isset($obj->pub_date) == false || $pub_date != $obj->pub_date)) {
				$updates[RSS::pub_date] = $pub_date;
			}

			if (isset($encl_url) && (isset($obj->enclosure_url) == false || $encl_url != $obj->enclosure_url)) {
				$updates[RSS::enclosure_url] = $encl_url;
			}

			if (isset($encl_length) && (isset($obj->enclosure_length) == false || $encl_length != $obj->enclosure_length)) {
				$updates[RSS::enclosure_length] = $encl_length;
			}

			if (isset($encl_mime) && (isset($obj->enclosure_mime) == false || $encl_mime != $obj->enclosure_mime)) {
				$updates[RSS::enclosure_mime] = $encl_mime;
			}

			if (isset($encl_hash) && (isset($obj->enclosure_hash) == false || $encl_hash != $obj->enclosure_hash)) {
				$updates[RSS::enclosure_hash] = $encl_hash;
			}

			if (isset($encl_password) && (isset($obj->enclosure_password) == false || boolval($encl_password) != boolval($obj->enclosure_password))) {
				$updates[RSS::enclosure_password] = (($encl_password) ? 1 : 0);
			}

			if ( count($updates) > 0 ) {
				$obj = $this->updateObject( $obj, $updates );
			}

			return $obj;
		}
		return false;
	}
}
?>
