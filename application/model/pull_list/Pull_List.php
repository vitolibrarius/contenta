<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_ListDBO as Pull_ListDBO;

/** Sample Creation script
		$sql = "CREATE TABLE IF NOT EXISTS " . pull_list . " ( "
			. Pull_List::id . " INTEGER PRIMARY KEY, "
			. Pull_List::name . " TEXT, "
			. Pull_List::etag . " TEXT, "
			. Pull_List::created . " INTEGER, "
			. Pull_List::published . " INTEGER, "
			. Pull_List::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List::endpoint_id .") REFERENCES " . model\networking\Endpoint::TABLE . "(" . model\networking\Endpoint::id . "),"
			. ")";
		$this->sqlite_execute( "pull_list", $sql, "Create table pull_list" );
*/
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

	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Pull_List::name, $value);
	}

	public function allLikeName($value)
	{
		return \SQL::Select( $this )
			->where( Qualifier::Like( Pull_List::name, normalizeSearchString($value), SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
	public function allForEtag($value)
	{
		return $this->allObjectsForKeyValue(Pull_List::etag, $value);
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

	// to-many relationship
	public function pull_list_items()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('model\pull_list\Pull_List_Item');
			return $model->allObjectsForKeyValue( model\pull_list\Pull_List_Item::pull_list_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function exclusions()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('model\pull_list\Pull_List_Exclusion');
			return $model->allObjectsForKeyValue( model\pull_list\Pull_List_Exclusion::endpoint_id, $this->endpoint_id);
		}

		return false;
	}

	// to-many relationship
	public function expansions()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('model\pull_list\Pull_List_Expansion');
			return $model->allObjectsForKeyValue( model\pull_list\Pull_List_Expansion::endpoint_id, $this->endpoint_id);
		}

		return false;
	}

}

?>
