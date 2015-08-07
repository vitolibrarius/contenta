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
/**
sqlite> SELECT a.id,a.name,a.media_count
FROM publication AS a
	where (a.series_id in ( select id from series where pub_wanted = 1)
		or a.id in ( select c.publication_id from story_arc_publication AS c, story_arc AS b where c.story_arc_id = b.id   and b.pub_wanted = 1))
	and (a.media_count is null or a.media_count = 0);
*/

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

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( $model->sortOrder() );

// 			$select = SQL::SelectJoin($model, null, $otherPubQual);
// 			$select->joinOn(
// 				Model::Named("Publication"),
// 				Model::Named("Series"),
// 				null,
// 				Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE)
// 			);
// 			$select->joinOn(
// 				Model::Named("Publication"),
// 				Model::Named("Story_Arc_Publication"),
// 				null,
// 				null
// 			);
// 			$select->joinOn(
// 				Model::Named("Story_Arc_Publication"),
// 				Model::Named("Story_Arc"),
// 				null,
// 				Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE)
// 			);

// 			$select->orderBy( $model->sortOrder() );
//
 						Session::addPositiveFeedback("select ". $select);

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
			$this->view->render( '/wanted/wanted', true);
		}
	}
}
