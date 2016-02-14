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
use processor\FluxImporter as FluxImporter;

use controller\Admin as Admin;
use connectors\NewznabConnector as NewznabConnector;

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
	/**
		sqlite> select s.id, s.name, count(p.id) from
   ...> series s, publication p
   ...> where s.id = p.series_id
   ...> and (p.media_count is null or p.media_count = 0)
   ...> group by s.id, s.name
   ...> having count(p.id) > 0;
	*/
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$pub_model = Model::Named('Publication');

			$select = SQL::Select($pub_model);
			$select->where(Qualifier::AndQualifier(
				Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE ),
				Qualifier::OrQualifier(
					Qualifier::IsNull( Series::pub_count),
					Qualifier::IsNull( Series::pub_available ),
					Qualifier::AttributeCompare( Series::pub_count, Qualifier::GREATER_THAN, Series::pub_available )
					)
				)
			);
			$select->orderBy( array( array(SQL::SQL_ORDER_ASC => Series::name)));
			$select->limit(0);

			//Session::addPositiveFeedback("select ". $select);

			$this->view->model = $pub_model;
			$this->view->render( '/wanted/index');
		}
	}

	function index_series()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$series_model = Model::Named('Series');

			$select = SQL::Select($series_model);
			$select->where(Qualifier::AndQualifier(
				Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE ),
				Qualifier::OrQualifier(
					Qualifier::IsNull( Series::pub_count),
					Qualifier::IsNull( Series::pub_available ),
					Qualifier::AttributeCompare( Series::pub_count, Qualifier::GREATER_THAN, Series::pub_available )
					)
				)
			);
			$select->orderBy( array( array(SQL::SQL_ORDER_ASC => Series::name)));
			$select->limit(0);

			//Session::addPositiveFeedback("select ". $select);

			$this->view->model = $series_model;
			$this->view->listArray = $select->fetchAll();
			$this->view->render( '/wanted/index_series');
		}
	}

	function index_story_arc()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Story_Arc');

			$select = SQL::Select($model);
			$select->where(Qualifier::AndQualifier(
				Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE ),
				Qualifier::OrQualifier(
					Qualifier::IsNull( Series::pub_count),
					Qualifier::IsNull( Series::pub_available ),
					Qualifier::AttributeCompare( Series::pub_count, Qualifier::GREATER_THAN, Series::pub_available )
					)
				)
			);
			$select->orderBy( array( array(SQL::SQL_ORDER_ASC => Series::name)));
			$select->limit(0);

			//Session::addPositiveFeedback("select ". $select);

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
			$this->view->render( '/wanted/index_story_arc');
		}
	}

	function pubsWanted()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Publication');

			$qualifiers[] = Qualifier::OrQualifier(
				Qualifier::Equals( Publication::media_count, 0 ),
				Qualifier::IsNull( Publication::media_count )
			);

			if ( isset($_GET['date_range']) ) {
				$monthRange = intval($_GET['date_range']);
				if ( $monthRange >= 0 ) {
					$start_date = new \DateTime();
					$start_date->modify( '-'.$monthRange.' month' );
					$end_date = clone $start_date;
					$start_date->modify('first day of this month');
					$end_date->modify('last day of this month');

					if ( $monthRange == 0 ) {
						$qualifiers[] = Qualifier::GreaterThanEqual( Publication::pub_date,
							$start_date->getTimeStamp()
						);
					}
					else {
						$qualifiers[] = Qualifier::Between( Publication::pub_date,
							$start_date->getTimeStamp(),
							$end_date->getTimeStamp()
						);
					}

					$series_model = Model::Named('Series');
					$qualifiers[] = Qualifier::InSubQuery( Publication::series_id,
						SQL::Select($series_model, array("id"))
						->where( Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE ) )
						->limit(0)
					);
				}
			}

			if ( isset($_GET['series_id']) ) {
				$qualifiers[] = Qualifier::Equals( Publication::series_id, $_GET['series_id']);
			}
			if ( isset($_GET['story_arc_id']) ) {
				$saj_model = Model::Named('Story_Arc_Publication');
				$qualifiers[] = Qualifier::InSubQuery( Publication::id,
					SQL::Select($saj_model, array("publication_id"))
						->where( Qualifier::Equals( "story_arc_id", $_GET['story_arc_id']))
						->limit(0)
				);
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( array(
					array(SQL::SQL_ORDER_DESC => Publication::issue_num)
				)
			);

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
			$this->view->render( '/wanted/wanted', true);
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
			$select->orderBy( array(
					array(SQL::SQL_ORDER_ASC => Publication::series_id),
					array(SQL::SQL_ORDER_DESC => Publication::issue_num)
				)
			);

//  						Session::addPositiveFeedback("select ". $select);

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
			$this->view->render( '/wanted/wanted', true);
		}
	}

	function newznab($pubId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			if ( $pubId > 0 ) {
				$publication = Model::Named('Publication')->objectForId( $pubId );
				if ( $publication != false ) {
					$this->view->searchString = $publication->searchString();
				}
			}

			$model = Model::Named('Endpoint');
			$this->view->endpoints = $model->allForTypeCode(Endpoint_Type::Newznab);
			$this->view->setLocalizedViewTitle("Search Newznab");
			$this->view->controllerAction = "newznab";
			$this->view->model = $model;
			$this->view->render( '/wanted/newznab');
		}
	}

	function searchNewznab()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( isset($_GET['endpoint_id'], $_GET['search']) && strlen($_GET['search']) > 4) {
				$model = Model::Named('Endpoint');
				$endpoint = $model->objectForId( $_GET['endpoint_id'] );

				try {
					$connection = new NewznabConnector( $endpoint );
					$this->view->endpoint_id = $_GET['endpoint_id'];
					$this->view->fluxModel = Model::Named('Flux');
					$this->view->results = $connection->searchComics($_GET['search']);
				}
				catch ( \Exception $e ) {
					Session::addNegativeFeedback( $e->getMessage() );
				}
			}
			$this->view->render( '/wanted/newznab_results', true);
		}
	}

	function newznabQuicksearch($pubId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$points = Model::Named('Endpoint')->allForTypeCode(Endpoint_Type::Newznab);
			if ( $points == false || count($points) == 0) {
				echo '<section class="feedback error">' . Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) . Endpoint_Type::Newznab. '</section>';
			}
			else if ( $pubId > 0 ) {
				$publication = Model::Named('Publication')->objectForId( $pubId );
				if ( $publication != false ) {
					$endpoint = $points[0];
					try {
						$connection = new NewznabConnector( $endpoint );
						$this->view->endpoint_id = $endpoint->id;
						$this->view->fluxModel = Model::Named('Flux');
						$results = $connection->searchComics($publication->searchString());
						if ( is_array($results) == false ) {
							$results = $connection->searchComics($publication->seriesName() . " " . $publication->paddedIssueNum());
						}
						$this->view->results = $results;
						$this->view->render( '/wanted/newznab_quick', true);
					}
					catch ( \Exception $e ) {
						echo '<section class="feedback error">' . $e->getMessage(). '</section>';
					}
				}
				else {
					echo '<section class="feedback error">No publication found</section>';
				}
			}
			else {
				echo '<section class="feedback error">No publication identified</section>';
			}
		}
	}

	function downloadNewznab()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( isset($_GET['endpoint_id'], $_GET['name'], $_GET['guid'], $_GET['url'], $_GET['postedDate'])) {
				$name = $_GET['name'];
				$endpoint_id = $_GET['endpoint_id'];
				$guid = $_GET['guid'];
				$url = $_GET['nzburl'];
				$postedDate = $_GET['postedDate'];

				$points = Model::Named('Endpoint')->allForTypeCode(Endpoint_Type::SABnzbd);
				if ( $points == false || count($points) == 0) {
					Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) . Endpoint_Type::SABnzbd);
					header('location: ' . Config::Web('/netconfig/index'));
				}
				else {
					$source = Model::Named('Endpoint')->objectForId($endpoint_id);

					$fluxImporter = new FluxImporter();
					$fluxImporter->setEndpoint( $points[0] );
					$fluxImporter->importFluxValues( $source, $name, $guid, $postedDate, $url );
					$fluxImporter->daemonizeProcess();

					echo "<em>Importing ..</em>";
				}
			}
			else {
				echo "nope";
			}
		}
	}
}
