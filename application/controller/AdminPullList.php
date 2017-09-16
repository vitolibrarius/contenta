<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;
use \http\PageParams as PageParams;

use \utilities\MediaFilename as MediaFilename;

use controller\Admin as Admin;

use \model\network\Endpoint_Type as Endpoint_Type;
use \model\user\Users as Users;
use \model\media\Publisher as Publisher;
use \model\network\Rss as Rss;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;

/**
 * Class AdminPullList
 * The index controller
 */
class AdminPullList extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			$this->view->params = $parameters;

			$model = Model::Named('Pull_List_Item');
			$this->view->model = $model;
			$this->view->objects = $model->allObjects();
			$this->view->render( '/admin/pullListIndex');
		}
	}

	function searchPullLists($pageNum = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			$parameters->setPageSize(18);
			list( $hasNewValues, $query) = $parameters->updateParametersFromGET( array(
				'searchPullList', 'searchName' )
			);

			if ( isset($query['searchPullList']) || isset($query['searchName']) ) {
				$model = Model::Named('Pull_List_Item');
				$qualifiers = array();
				if ( isset($query['searchName']) && strlen($query['searchName']) > 0) {
					$qualifiers[] = Qualifier::Like( Pull_List_Item::search_name, normalizeSearchString($query['searchName']) );
				}

				if ( isset($query['searchPullList']) && intval($query['searchPullList']) > 0 ) {
					$qualifiers[] = Qualifier::Equals( Pull_List_Item::pull_list_id, $query['searchPullList'] );
				}

				if ( $hasNewValues ) {
					if ( count($qualifiers) > 0 ) {
						$count = SQL::Count( $model, null, Qualifier::AndQualifier( $qualifiers ) )->fetch();
					}
					else {
						$count = SQL::Count( $model )->fetch();
					}

					$parameters->queryResults($count->count);
				}
				else {
					if ( is_null( $pageNum) ) {
						$pageNum = $parameters->valueForKey( PageParams::PAGE_SHOWN, 0 );
					}
					else {
						$parameters->setValueForKey( PageParams::PAGE_SHOWN, $pageNum );
					}
				}

				$select = SQL::Select($model);
				if ( count($qualifiers) > 0 ) {
					$select->where( Qualifier::AndQualifier( $qualifiers ));
				}
				$select->limit($parameters->pageSize());
				$select->offset($parameters->pageShown());
				$select->orderBy( $model->sortOrder() );

				$this->view->model = $model;
				$this->view->params = $parameters;
				$this->view->listArray = $select->fetchAll();
			}
			$this->view->render( '/admin/pullListItems', true);
		}
	}

	function rssindex()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			$this->view->params = $parameters;

			$this->view->setLocalizedViewTitle("RSS");
			$this->view->controllerAction = "rssindex";
			$ep_model = Model::Named('Endpoint');
			$this->view->endpoints = $ep_model->allForTypeCode(Endpoint_Type::RSS, true);

			$model = Model::Named('Rss');
			$this->view->model = $model;
			$this->view->render( '/admin/rssIndex');
		}
	}

	function searchRss($pageNum = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			$parameters->setPageSize(16);
			list( $hasNewValues, $query) = $parameters->updateParametersFromGET( array(
				'searchName', 'searchIssue', 'searchYear', 'searchAge', 'searchSize', 'endpoint_id' )
			);

			$model = Model::Named('Rss');
			$qualifiers = array();

			if ( isset($query['searchName']) && strlen($query['searchName']) > 0) {
				$qualifiers[] = Qualifier::Like( Rss::clean_name, $query['searchName']);
			}
			if ( isset($query['searchIssue']) && strlen($query['searchIssue']) > 0) {
				$qualifiers[] = Qualifier::Equals( Rss::clean_issue, $query['searchIssue'] );
			}
			if ( isset($query['searchYear']) && strlen($query['searchYear']) == 4) {
				$qualifiers[] = Qualifier::Equals( Rss::clean_year, $query['searchYear'] );
			}
			if ( isset($query['searchAge']) && intval($query['searchAge']) > 0 ) {
				$ageTime = 86400 * intval($query['searchAge']);
				$qualifiers[] = Qualifier::GreaterThan( Rss::pub_date, (time() - $ageTime) );
			}
			if ( isset($query['searchSize']) && intval($query['searchSize']) > 0 ) {
				$enclosure_length = intval($query['searchSize']) * MEGABYTE;
				$qualifiers[] = Qualifier::GreaterThan( Rss::enclosure_length, $enclosure_length );
			}
			if ( isset($query['endpoint_id']) && intval($query['endpoint_id']) > 0 ) {
				$qualifiers[] = Qualifier::Equals( Rss::endpoint_id, $query['endpoint_id'] );
			}

			if ( $hasNewValues ) {
				if ( count($qualifiers) > 0 ) {
					$count = SQL::Count( $model, null, Qualifier::AndQualifier( $qualifiers ) )->fetch();
				}
				else {
					$count = SQL::Count( $model )->fetch();
				}

				$parameters->queryResults($count->count);
			}
			else {
				if ( is_null( $pageNum) ) {
					$pageNum = $parameters->valueForKey( PageParams::PAGE_SHOWN, 0 );
				}
				else {
					$parameters->setValueForKey( PageParams::PAGE_SHOWN, $pageNum );
				}
				$count = SQL::Count( $model )->fetch();
				$parameters->queryResults($count->count);
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->limit($parameters->pageSize());
			$select->offset($parameters->pageShown());
			$select->orderBy( $model->sortOrder() );

			$this->view->model = $model;
			$this->view->params = $parameters;
			$this->view->listArray = $select->fetchAll();
// 			$this->view->toggleWantedAction = "/AdminSeries/toggleWantedSeries";
// 			$this->view->editAction = "/AdminSeries/editSeries";
// 			$this->view->wantedAction = "/AdminSeries/toggleWantedSeries";
			$this->view->render( '/admin/rssItems', true);
		}
	}
}
