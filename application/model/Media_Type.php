<?php

namespace model;

use \Database as Database;
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class Media_Type extends Model
{
	const TABLE =		'media_type';
	const id =			'id';
	const name =		'name';
	const code =		'code';

	// currently available type codes
	const CBZ =		"cbz";
	const CBR =		"cbr";
	const EPUB =	"epub";
	const PDF =		"pdf";

	public function cbz() 		{ return $this->mediaTypeForCode( Media_Type::CBZ ); }

	public function tableName() { return Media_Type::TABLE; }
	public function tablePK() 	{ return Media_Type::id; }
	public function sortOrder() { return array(Media_Type::name); }

	public function allColumnNames()
	{
		return array(
			Media_Type::id, Media_Type::name, Media_Type::code
		 );
	}

	function mediaTypeForCode($cd)
	{
		return $this->singleObjectForKeyValue( Media_Type::code, $cd);
	}
}

?>
