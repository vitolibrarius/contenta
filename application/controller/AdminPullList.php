<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \utilities\MediaFilename as MediaFilename;

use controller\Admin as Admin;

use \model\user\Users as Users;
use \model\media\Publisher as Publisher;
use \model\network\Rss as Rss;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;

use \SQL as SQL;
use db\Qualifier as Qualifier;

/**
 * Class AdminPullList
 * The index controller
 */
class AdminPullList extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Pull_List_Item');
			$this->view->model = $model;
			$this->view->objects = $model->allObjects();
			$this->view->render( '/admin/pullListIndex');
		}
	}

	function searchPullLists()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$pull_list_id = HttpGet::get('pull_list_id', false);
			$name = HttpGet::get('name', false);

			if ( $pull_list_id != false || $name != false) {
				$model = Model::Named('Pull_List_Item');
				$qualifiers = array();

				if ( $pull_list_id != false ) {
					$qualifiers[] = Qualifier::Equals( Pull_List_Item::pull_list_id, $pull_list_id );
				}

				if ( $name != false ) {
					$qualifiers[] = Qualifier::Like( Pull_List_Item::search_name, normalizeSearchString($name));
				}

				$select = SQL::Select($model);
				if ( count($qualifiers) > 0 ) {
					$select->where( Qualifier::AndQualifier( $qualifiers ));
				}
				$select->limit( -1 );
				$select->orderBy( $model->sortOrder() );

				$this->view->model = $model;
				$this->view->listArray = $select->fetchAll();
			}
			$this->view->render( '/admin/pullListItems', true);
		}
	}

	function rssindex()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->setLocalizedViewTitle("RSS");
			$this->view->controllerAction = "rssindex";
			$model = Model::Named('Rss');
			$this->view->model = $model;
			$this->view->render( '/admin/rssIndex');
		}
	}

	function searchRss()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Rss');
			$qualifiers = array();

			if ( isset($_GET['name']) && strlen($_GET['name']) > 0) {
				$qualifiers[] = Qualifier::Like( Rss::clean_name, $_GET['name']);
			}
			if ( isset($_GET['issue']) && strlen($_GET['issue']) > 0) {
				$qualifiers[] = Qualifier::Equals( Rss::clean_issue, $_GET['issue'] );
			}
			if ( isset($_GET['year']) && strlen($_GET['year']) == 4 ) {
				$qualifiers[] = Qualifier::Equals( Rss::clean_year, $_GET['year'] );
			}
			if ( isset($_GET['age']) && intval($_GET['age']) > 0 ) {
				$ageTime = 86400 * intval($_GET['age']);
				$qualifiers[] = Qualifier::GreaterThan( Rss::pub_date, (time() - $ageTime) );
			}
			if ( isset($_GET['size']) && intval($_GET['size']) > 0 ) {
				$enclosure_length = intval($_GET['size']) * MEGABYTE;
				$qualifiers[] = Qualifier::GreaterThan( Rss::enclosure_length, $enclosure_length );
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( $model->sortOrder() );

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
// 			$this->view->toggleWantedAction = "/AdminSeries/toggleWantedSeries";
// 			$this->view->editAction = "/AdminSeries/editSeries";
// 			$this->view->wantedAction = "/AdminSeries/toggleWantedSeries";
			$this->view->render( '/admin/rssItems', true);
		}
	}
}
