<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class UsersDBO extends DataObject
{
	public $name;
	public $password_hash;
	public $email;
	public $active;
	public $account_type;
	public $rememberme_token;
	public $creation_timestamp;
	public $last_login_timestamp;
	public $failed_logins;
	public $last_failed_login;
	public $activation_hash;
	public $api_hash;
	public $password_reset_hash;
	public $password_reset_timestamp;

	public function isAdmin() {
		return (isset($this->account_type) && $this->account_type === Users::AdministratorRole);
	}

	public function isActive() {
		return (isset($this->active) && $this->active == 1);
	}

	public function recordLoginFrom($ip) {
		$join_model = Model::Named('User_Network');
		$join = $join_model->createForIP($this, $ip);
		return $join;
	}

	public function allLoginIP() {
		$join_model = Model::Named('User_Network');
		$array = $join_model->allForUser($this);
		return $array;
	}

	public function tags() {
		$tag_model = Model::Named('Tag');
		$model = Model::Named('TagJoin');
		$joins = $model->allForObject($this);

		if ( $joins != false ) {
			return $this->model()->fetchAllJoin(
				TagModel::TABLE,
				$tag_model->allColumns(),
				TagModel::id, TagJoinModel::tag_id, $joins, null, array(TagModel::name));
		}
		return false;
	}

	public function addTag($name = null) {
		if ( isset($name) ) {
			$model = Model::Named('TagJoin');
			return $model->createTag($this, $name);
		}
		return false;
	}

	public function seriesBeingRead() {
		$join_model = Model::Named('UserSeriesJoin');
		$joins = $join_model->allSeriesForUser($this);
		return $joins;
	}

	public function mediaJoins() {
		$join_model = Model::Named('UserMediaJoin');
		$joins = $join_model->allForUser($this);
		return $joins;
	}

	public function mediaJoin($media = null) {
		if ( isset($media) ) {
			$join_model = Model::Named('UserMediaJoin');
			return $join_model->create($this, $media);
		}
		return false;
	}

	public function flagMediaAsRead($media = null) {
		if ( isset($media) ) {
			$join_m_model = Model::Named('UserMediaJoin');
			$join_s_model = Model::Named('UserSeriesJoin');

			$join_m_model->create($this, $media, Model::TERTIARY_TRUE, null);
			$join_s_model->create($this, $media->publication()->series(), Model::TERTIARY_TRUE, null);
		}
		return false;
	}

	public function flagMediaAsMislabled($media = null) {
		if ( isset($media) ) {
			$join_m_model = Model::Named('UserMediaJoin');
			$join_s_model = Model::Named('UserSeriesJoin');

			$join_m_model->create($this, $media, null, Model::TERTIARY_TRUE);
			$join_s_model->create($this, $media->publication()->series(), null, Model::TERTIARY_TRUE);
		}
		return false;
	}
}

?>
