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
			$model = Model::Named('Book');
			$qualifiers = array();
			if ( isset($_GET['name']) && strlen($_GET['name']) > 0) {
				$qualifiers[] = Qualifier::Like( Book::name, normalizeSearchString($_GET['name']));
			}
			if ( isset($_GET['author']) && strlen($_GET['author']) > 0 ) {
				$qualifiers[] = Qualifier::Like( Book::author, normalizeSearchString($_GET['author']));
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( $model->sortOrder() );

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
			$this->view->render( '/book/bookCards', true);
		}
	}
}
