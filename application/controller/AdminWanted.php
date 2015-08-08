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
use \Processor as Processor;

use processor\ComicVineImporter as ComicVineImporter;
use processor\UploadImport as UploadImport;
use processor\ImportManager as ImportManager;

use controller\Admin as Admin;

use model\Users as Users;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Publication as Publication;
use model\Series as Series;

use \SQL as SQL;
use db\Qualifier as Qualifier;

class AdminWanted extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Publication');
			$this->view->model = $model;
			$this->view->render( '/wanted/index');
		}
	}

	function searchWanted()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publication');
			$series_model = Model::Named('Series');
			$saj_model = Model::Named('Story_Arc_Publication');
			$otherPubQual = null;
			$qualifiers[] = Qualifier::OrQualifier(
				Qualifier::Equals( Publication::media_count, 0 ),
				Qualifier::IsNull( Publication::media_count )
			);
			$qualifiers[] = Qualifier::OrQualifier(
				Qualifier::InSubQuery( Publication::series_id,
					SQL::Select($series_model, array("id"))->where(Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE ))->limit(0)
				),
				Qualifier::InSubQuery( Publication::id,
					SQL::SelectJoin($saj_model, array("publication_id"))
						->joinOn( $saj_model, Model::Named("Story_Arc"), null, Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE))
						->limit(0)
				)
			);

			// input filters
			if ( isset($_GET['story_arc_id']) && is_array($_GET['story_arc_id']) && count($_GET['story_arc_id']) > 0 ) {
				$qualifiers[] = Qualifier::InSubQuery( Publication::id,
					SQL::Select($saj_model, array("publication_id"))
						->where( Qualifier::IN( "story_arc_id", $_GET['story_arc_id']))
						->limit(0)
				);
			}
			if ( isset($_GET['year']) && strlen($_GET['year']) == 4 ) {
				$start = strtotime("01-01-" . $_GET['year'] . " 00:00");
				$end = strtotime("31-12-" . $_GET['year'] . " 23:59");
				$qualifiers[] = Qualifier::Between( Publication::pub_date, $start, $end );
			}
			if ( isset($_GET['issue']) && strlen($_GET['issue']) > 0) {
				$qualifiers[] = Qualifier::Equals( Publication::issue_num, $_GET['issue'] );
			}
			if ( isset($_GET['series_name']) && strlen($_GET['series_name']) > 0) {
				$select = \SQL::Select( Model::Named('Series'), array(Series::id))
					->where( Qualifier::Like( Series::search_name, normalizeSearchString($_GET['series_name'])) );
				$series_idArray = array_map(function($stdClass) {return $stdClass->{Series::id}; },
					$select->fetchAll());

				if ( is_array($series_idArray) && count($series_idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Publication::series_id, $series_idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Publication::id, 0 );
				}
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( array( array(SQL::SQL_ORDER_DESC => Publication::pub_date)) );

//  						Session::addPositiveFeedback("select ". $select);

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
			$this->view->render( '/wanted/wanted', true);
		}
	}
}
