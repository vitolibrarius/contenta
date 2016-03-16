<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

/** Sample Creation script
		$sql = "CREATE TABLE IF NOT EXISTS " . pull_list_expansion . " ( "
			. Pull_List_Expansion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Expansion::pattern . " TEXT, "
			. Pull_List_Expansion::replace . " TEXT, "
			. Pull_List_Expansion::created . " INTEGER, "
			. Pull_List_Expansion::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Expansion::endpoint_id .") REFERENCES " . model\networking\Endpoint::TABLE . "(" . model\networking\Endpoint::id . "),"
			. ")";
		$this->sqlite_execute( "pull_list_expansion", $sql, "Create table pull_list_expansion" );
*/
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

	public function allForPattern($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::pattern, $value);
	}

	public function allForReplace($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::replace, $value);
	}



	// to-one relationship
	public function endpoint()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('model\networking\Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}

}

?>
