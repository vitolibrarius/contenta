<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;

/** Sample Creation script
		$sql = "CREATE TABLE IF NOT EXISTS " . pull_list_excl . " ( "
			. Pull_List_Exclusion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Exclusion::pattern . " TEXT, "
			. Pull_List_Exclusion::type . " TEXT, "
			. Pull_List_Exclusion::created . " INTEGER, "
			. Pull_List_Exclusion::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Exclusion::endpoint_id .") REFERENCES " . model\networking\Endpoint::TABLE . "(" . model\networking\Endpoint::id . "),"
			. ")";
		$this->sqlite_execute( "pull_list_excl", $sql, "Create table pull_list_excl" );
*/
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

	public function allForPattern($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Exclusion::pattern, $value);
	}

	public function allForType($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Exclusion::type, $value);
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
