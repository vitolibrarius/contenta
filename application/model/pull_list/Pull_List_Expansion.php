<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

class Pull_List_Expansion extends Model
{
	const TABLE = 'pull_list_expansion';
	const id = 'id';
	const pattern = 'pattern';
	const replace = 'replace';
	const created = 'created';
	const endpoint_id = 'endpoint_id';

	public function tableName() { return Pull_List_Expansion::TABLE; }
	public function tablePK() { return Pull_List_Expansion::id; }
	public function sortOrder() { return array( 'asc' => array(Pull_List_Expansion::pattern, )); }

	public function allColumnNames()
	{
		return array(
Pull_List_Expansion::id, Pull_List_Expansion::pattern, Pull_List_Expansion::replace, Pull_List_Expansion::created, Pull_List_Expansion::endpoint_id, 		 );
	}
}

?>
