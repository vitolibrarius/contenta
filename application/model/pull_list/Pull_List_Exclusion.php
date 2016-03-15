<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;

class Pull_List_Exclusion extends Model
{
	const TABLE = 'pull_list_excl';
	const id = 'id';
	const pattern = 'pattern';
	const type = 'type';
	const created = 'created';
	const endpoint_id = 'endpoint_id';

	public function tableName() { return Pull_List_Exclusion::TABLE; }
	public function tablePK() { return Pull_List_Exclusion::id; }
	public function sortOrder() { return array( 'asc' => array(Pull_List_Exclusion::pattern, )); }

	public function allColumnNames()
	{
		return array(
Pull_List_Exclusion::id, Pull_List_Exclusion::pattern, Pull_List_Exclusion::type, Pull_List_Exclusion::created, Pull_List_Exclusion::endpoint_id, 		 );
	}
}

?>
