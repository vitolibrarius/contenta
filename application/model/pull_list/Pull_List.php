<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_ListDBO as Pull_ListDBO;

class Pull_List extends Model
{
	const TABLE = 'pull_list';
	const id = 'id';
	const name = 'name';
	const etag = 'etag';
	const created = 'created';
	const published = 'published';
	const endpoint_id = 'endpoint_id';

	public function tableName() { return Pull_List::TABLE; }
	public function tablePK() { return Pull_List::id; }
	public function sortOrder() { return array( 'asc' => array(Pull_List::name, )); }

	public function allColumnNames()
	{
		return array(
Pull_List::id, Pull_List::name, Pull_List::etag, Pull_List::created, Pull_List::published, Pull_List::endpoint_id, 		 );
	}
}

?>
