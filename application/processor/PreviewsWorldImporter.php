<?php

namespace processor;

use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SimpleXMLElement as SimpleXMLElement;

use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_List_Group as Pull_List_Group;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;
use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;

use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

class PreviewsWorldImporter extends EndpointImporter
{
	public $expansions = null;
	public $items_excluded = null;

	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function isGroupInExclusions( $groupname = "none" )
	{
		$type = $this->endpoint()->endpointType();
		$matches = Model::Named( "Pull_List_Exclusion" )->objectsForPatternTypeAndEndpointType( $groupname, Pull_List_Exclusion::GROUP_TYPE, $type->code );
		return (is_array($matches) && count($matches) > 0);
	}

	public function isItemInExclusions( $itemname = "none" )
	{
		if ( $this->items_excluded == null ) {
			$type = $this->endpoint()->endpointType();
			$this->items_excluded = Model::Named( "Pull_List_Exclusion" )->objectsForTypeAndEndpointType( Pull_List_Exclusion::ITEM_TYPE, $type->code );
		}

		if (is_null($itemname) == false) {
			foreach( $this->items_excluded as $excl ) {
				$pattern = $excl->pattern;
				if (contains($pattern, $itemname )) {
					return true;
				}
			}
		}
		return false;
	}

	public function expandItemName( $itemname = "none" )
	{
		if ( $this->expansions == null ) {
			$type = $this->endpoint()->endpointType();
			$this->expansions = Model::Named( "Pull_List_Expansion" )->allForEndpoint_type( $type );
		}

		if ( is_array($this->expansions) ) {
			foreach( $this->expansions as $pl_expand ) {
				$quoted = $pl_expand->preg_quoted_pattern();
				if ( $quoted != null ) {
					$replacement = preg_replace('/(?:'.$quoted.')+/', $pl_expand->replace(), $itemname);
					if ( $itemname != $replacement ) {
						$itemname = $replacement;
					}
				}
			}
		}
		return $itemname;
	}

	public function processData()
	{
		$connection = $this->endpointConnector();
		$endpoint = $this->endpoint();
		$releaseDate = null;
		$duplicateIndex = array();

		list( $text, $headers ) = $connection->performGET( $endpoint->base_url );
		if ( empty($text) == true ) {
			throw new \Exception( "No PreviewsWorld data" );
		}

		if ( isset($headers, $headers['ETag']) ) {
			$etag = $headers['ETag'];
		}
		else {
			$etag = md5($text);
		}

		$pulllist = Model::Named("Pull_List")->objectForEtag($etag);
		if ( $pulllist != false ) {
			$this->setPurgeOnExit(true);
			return;
		}
		else {
			list($pulllist, $errors) = Model::Named("Pull_List")->createObject( array(
				Pull_List::name => $this->endpoint()->name(),
				Pull_List::etag => $etag,
				Pull_List::published => time(),
				"endpoint" => $this->endpoint()
				)
			);
		}

		$dataArray = preg_split('/\n|\r/', $text, -1, PREG_SPLIT_NO_EMPTY);
		$groupList = array_kmap(
			function($k, $v) {
				$split = explode("\t", $v);
				return $split;
			},
			$dataArray
		);

		$count = 0;
		$currentGroupName = "error";
		foreach( $groupList as $line ) {
			if ( is_array($line) ) {
				if ( count($line) == 1 ) {
					$currentGroupName = $line[0];
					if ( startsWith('New Releases For ', $currentGroupName)) {
						$releaseDate = substr($currentGroupName, strlen('New Releases For '));
						$releaseDate = strtotime($releaseDate);
						$pulllist->setName( $currentGroupName );
						$pulllist->setPublished( $releaseDate );
						$pulllist->saveChanges();
					}
				}
				else if( $this->isGroupInExclusions($currentGroupName) == false) {
					$code = $line[0];
					$item = $line[1];
					$price = $line[2];
					if ( $price != 'PI' ) {
						if ( $this->isItemInExclusions( $item ) == false ) {
							$item = $this->expandItemName($item);
							list($pulllist_group, $errors) = Model::Named("Pull_List_Group")->createObject( array(
								Pull_List_Group::data => $currentGroupName
								)
							);

							list($pulllist_item, $errors) = Model::Named("Pull_List_Item")->createObject( array(
								Pull_List_Item::data => $line[1],
								"pull_list_group" => $pulllist_group,
								"pull_list" => $pulllist
								)
							);
							if ( $pulllist_item != false ) {
								$count++;
							}
						}
					}
				}
			}
			else {
				throw new PreviewsWorldException( "unknown line " . var_export($line, true) );
			}
		}

		if ( $count > 0 ) {
			\Logger::logInfo( $pulllist->name() . " created " . $count . " new items", $endpoint->name );
		}

		$this->setPurgeOnExit(true);
		return;
	}
}
