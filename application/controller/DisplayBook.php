<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \http\Session as Session;;
use \http\PageParams as PageParams;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;

use \model\user\Users as Users;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\media\Book as Book;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Series as Series;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_Character as Series_Character;
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

/**
 * Class Admin
 * The index controller
 */
class DisplayBook extends Controller
{
	function index()
	{
		if (Auth::handleLogin()) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Book');
			$this->view->model = $model;
			$this->view->render( '/book/index');
		}
	}

	function searchBooks($pageNum = 0)
	{
		if (Auth::handleLogin()) {
			$parameters = Session::pageParameters( $this, "index" );
			$parameters->setPageSize(12);
			list( $hasNewValues, $query) = $parameters->updateParametersFromGET( array(
				'searchName', 'searchAuthor' )
			);

			$model = Model::Named('Book');
			$qualifiers = array();
			if ( isset($query['searchName']) && strlen($query['searchName']) > 0 ) {
				$qualifiers[] = Qualifier::Like( Book::name, normalizeSearchString($query['searchName']));
			}
			if ( isset($query['searchAuthor']) && strlen($query['searchAuthor']) > 0 ) {
				$qualifiers[] = Qualifier::Like( Book::author, normalizeSearchString($query['searchAuthor']));
			}

			if ( count($qualifiers) > 0 ) {
				$count = SQL::Count( $model, null, Qualifier::AndQualifier( $qualifiers ) )->fetch();
			}
			else {
				$count = SQL::Count( $model )->fetch();
			}

			$parameters->queryResults($count->count);

			if ( $hasNewValues == false ) {
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
			$this->view->render( '/book/bookCards', true);
		}
	}
}
