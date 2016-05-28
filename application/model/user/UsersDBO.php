<?php

namespace model\user;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\user\Users as Users;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\User_Series as User_Series;
use \model\User_SeriesDBO as User_SeriesDBO;

class UsersDBO extends _UsersDBO
{

	public function recordLoginFromIp_address($ip = null)
	{
		$join_model = Model::Named('User_Network');
		return $join_model->createForIp_address($this, $ip);
	}

	public function allLoginIP() {
		$join_model = Model::Named('User_Network');
		$array = $join_model->allForUser($this);
		return $array;
	}

	public function seriesBeingRead($limit = 50) {
		$select = \SQL::SelectJoin( Model::Named("Series") );
		$select->joinOn( Model::Named("Series"), Model::Named("User_Series"), null,
			Qualifier::FK( User_Series::user_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Series"), Series::name);
		return $select->fetchAll();
	}

	public function addSeries($series = null) {
		if ( isset($series) ) {
			$model = Model::Named('User_Series');
			return $model->create($this, $series);
		}
		return false;
	}

}

?>
