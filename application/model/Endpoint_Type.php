<?php

namespace model;

use \Database as Database;
use \DataObject as DataObject;
use \Model as Model;

class Endpoint_Type extends Model
{
	const TABLE =		'endpoint_type';
	const id =			'id';
	const name =		'name';
	const code =		'code';
	const data_type =	'data_type';
	const site_url =	'site_url';
	const favicon_url = 'favicon_url';
	const api_url =		'api_url';
	const throttle_hits =	'throttle_hits';
	const throttle_time =	'throttle_time';
	const comments =		'comments';

	// currently available type codes
	const Newznab =		"Newznab";
	const RSS =			"RSS";
	const ComicVine =	"ComicVine";
	const SABnzbd =		"SABnzbd";


	public function tableName() { return Endpoint_Type::TABLE; }
	public function tablePK() { return Endpoint_Type::id; }
	public function sortOrder() { return array(Endpoint_Type::name); }

	public function allColumnNames()
	{
		return array(
			Endpoint_Type::id, Endpoint_Type::name, Endpoint_Type::code, Endpoint_Type::data_type,
			Endpoint_Type::site_url, Endpoint_Type::api_url, Endpoint_Type::comments, Endpoint_Type::favicon_url,
			Endpoint_Type::throttle_hits, Endpoint_Type::throttle_time
		 );
	}

	function endpointTypes()
	{
		return $this->fetchAll(Endpoint_Type::TABLE, $this->allColumnNames(), null, array(Endpoint_Type::name));
	}

	function endpointTypeForCode($name)
	{
		return $this->fetch(Endpoint_Type::TABLE, $this->allColumnNames(), array(Endpoint_Type::code => $name));
	}
}

?>
