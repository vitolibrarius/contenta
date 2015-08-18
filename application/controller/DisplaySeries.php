<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Session as Session;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;

use model\Users as Users;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series as Series;
use model\Series_Alias as Series_Alias;
use model\Series_Character as Series_Character;
use model\User_Series as User_Series;

/**
 * Class Admin
 * The index controller
 */
class DisplaySeries extends Controller
{
	function index()
	{
		if (Auth::handleLogin()) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Series');
			$this->view->model = $model;
			$this->view->render( '/series/index');
		}
	}

	function searchSeries()
	{
		if (Auth::handleLogin()) {
			$model = Model::Named('Series');
			$qualifiers = array();
			if ( isset($_GET['name']) && strlen($_GET['name']) > 0) {
				$qualifiers[] = Qualifier::Like( Series::search_name, normalizeSearchString($_GET['name']));
			}
			if ( isset($_GET['year']) && strlen($_GET['year']) == 4 ) {
				$qualifiers[] = Qualifier::Equals( Series::start_year, $_GET['year'] );
			}
			if ( isset($_GET['publisher_id']) && intval($_GET['publisher_id']) > 0 ) {
				$qualifiers[] = Qualifier::Equals( Series::publisher_id, $_GET['publisher_id'] );
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( $model->sortOrder() );

						Session::addPositiveFeedback("select ". $select);

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
 			$this->view->detailAction = "/DisplaySeries/details";
// 			$this->view->editAction = "/AdminSeries/editSeries";
// 			$this->view->deleteAction = "/AdminSeries/deleteSeries";
			$this->view->render( '/series/seriesCards', true);
		}
	}

	function details($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( $oid > 0 ) {
				$model = Model::Named('Series');
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$this->view->model = $model;
					$this->view->setViewTitle($object->name);
					$this->view->detail = $object;
					$this->view->render( '/series/seriesDetails' );
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
					$this->view->render('/error/index');
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
				$this->view->render('/error/index');
			}
		}
	}
}
