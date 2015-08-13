<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

use utilities\MediaFilename as MediaFilename;
use model\Endpoint as Endpoint;

class Rss extends Model
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

	public function tableName() { return Rss::TABLE; }
	public function tablePK() { return Rss::id; }
	public function sortOrder() { return array(Rss::title, Rss::pub_date); }

	public function allColumnNames()
	{
		return array(
			Rss::id, Rss::endpoint_id, Rss::created,
			Rss::title, Rss::desc, Rss::pub_date, Rss::guid, Rss::clean_name, Rss::clean_issue, Rss::clean_year,
			Rss::enclosure_url, Rss::enclosure_length, Rss::enclosure_mime, Rss::enclosure_hash, Rss::enclosure_password
		);
	}

	public function objectForEndpointGUID( EndpointDBO $endpoint = null, $guid )
	{
		return $this->singleObjectForKeyValues( array( Rss::endpoint_id => $endpoint->id, Rss::guid => $guid ) );
	}

	public function create( EndpointDBO $endpoint = null, $title, $desc, $pub_date, $guid, $encl_url = null, $encl_length = 0, $encl_mime = 'application/x-nzb', $encl_hash = null, $encl_password = false )
	{
		if ( isset($title, $guid) && is_null($endpoint) == false ) {
			$mediaFilename = new MediaFilename($title);
			$meta = $mediaFilename->updateFileMetaData(null);

			$params = array(
				Rss::created => time(),
				Rss::title => $title,
				Rss::desc => $desc,
				Rss::pub_date => $pub_date,
				Rss::guid => $guid,
				Rss::clean_name => $meta['name'],
				Rss::clean_issue => (isset($meta['issue']) ? $meta['issue'] : null),
				Rss::clean_year => (isset($meta['year']) ? $meta['year'] : null),
				Rss::enclosure_url => $encl_url,
				Rss::enclosure_length => $encl_length,
				Rss::enclosure_mime => $encl_mime,
				Rss::enclosure_hash => $encl_hash,
				Rss::enclosure_password => ($encl_password) ? 1 : 0,
			);

			if ( isset($endpoint) ) {
				$params[Rss::endpoint_id] = $endpoint->id;
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
			return $obj;
		}

		return false;
	}

	public function update( RssDBO $obj = null, $title, $desc, $pub_date, $encl_url = null, $encl_length = 0, $encl_mime = 'application/x-nzb', $encl_hash = null, $encl_password = false )
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($title) && (isset($obj->title) == false || $title != $obj->title)) {
				$mediaFilename = new MediaFilename($title);
				$meta = $mediaFilename->updateFileMetaData(null);

				$updates[Rss::title] = $title;
				$updates[Rss::clean_name] = $meta['name'];
				$updates[Rss::clean_issue] = (isset($meta['issue']) ? $meta['issue'] : null);
				$updates[Rss::clean_year] = (isset($meta['year']) ? $meta['year'] : null);
			}

			if (isset($desc) && strlen($desc) > 0) {
				if ( $desc != $obj->desc ) {
					$updates[Rss::desc] = strip_tags($desc);
				}
			}

			if (isset($pub_date) && (isset($obj->pub_date) == false || $pub_date != $obj->pub_date)) {
				$updates[Rss::pub_date] = $pub_date;
			}

			if (isset($encl_url) && (isset($obj->enclosure_url) == false || $encl_url != $obj->enclosure_url)) {
				$updates[Rss::enclosure_url] = $encl_url;
			}

			if (isset($encl_length) && (isset($obj->enclosure_length) == false || $encl_length != $obj->enclosure_length)) {
				$updates[Rss::enclosure_length] = $encl_length;
			}

			if (isset($encl_mime) && (isset($obj->enclosure_mime) == false || $encl_mime != $obj->enclosure_mime)) {
				$updates[Rss::enclosure_mime] = $encl_mime;
			}

			if (isset($encl_hash) && (isset($obj->enclosure_hash) == false || $encl_hash != $obj->enclosure_hash)) {
				$updates[Rss::enclosure_hash] = $encl_hash;
			}

			if (isset($encl_password) && (isset($obj->enclosure_password) == false || boolval($encl_password) != boolval($obj->enclosure_password))) {
				$updates[Rss::enclosure_password] = (($encl_password) ? 1 : 0);
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
