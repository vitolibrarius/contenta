<?php

namespace connectors;

use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Cache as Cache;
use \Database as Database;

use utilities\MediaFilename as MediaFilename;

use model\Endpoint as Endpoint;
use model\EndpointDBO as EndpointDBO;

class PreviewsWorldException extends \Exception {}

class PreviewsWorldConnector extends EndpointConnector
{
	private $document;
	private $releaseDate;

	public function __construct($endpoint)
	{
		parent::__construct($endpoint);
	}

	public function testConnnector()
	{
		return array(true, "");
	}

	public function releaseDate()
	{
		return $this->releaseDate;
	}

	public function groupsIgnored()
	{
		return array(
			'PREVIEWS',
			'Shipping',
			'Every Wednesday',
			'Please check with',
			'PREMIER PUBLISHERS',
			'BOOKS',
			'COLLECTIBLES',
			'MCFARLANE TOYS',
			'New Releases',
			'Upcoming Releases',
			'SUPPLIES'
		);
	}

	public function substituteNameMap()
	{
		return array(
			'GFT GRIMM FAIRY TALES' => 'GRIMM FAIRY TALES PRESENTS',
			'GFT' => 'GRIMM FAIRY TALES PRESENTS',
			'HELLRAISER' => 'CLIVE BARKER\'S HELLRAISER',
			'BTVS SEASON 9' => 'BUFFY THE VAMPIRE SLAYER SEASON NINE',
			'BTVS SEASON 10' => 'BUFFY THE VAMPIRE SLAYER SEASON 10',
			'SUPURBIA' => 'GRACE RANDOLPH\'S SUPURBIA',
			'SW ' => 'Star Wars ',
			'(MR)' => '',
			'(NOTE PRICE)' => ''
		);
	}

	public function substitute( $item = null )
	{
		if (is_null($item) == false) {
			foreach( $this->substituteNameMap() as $old => $sub ) {
				$quoted = preg_quote($old);
				$replacement = preg_replace('/(?:'.$quoted.')+/', $sub, $item);
				if ( $item != $replacement ) {
// 					echo "/////////// replaced // $item // $replacement " .PHP_EOL;
					$item = $replacement;
				}
			}
		}
		return $item;
	}

	public function excludeList()
	{
		return array(
			'2ND PTG', // 2nd printing
			'3RD PTG',
			'4TH PTG',
			'5TH PTG',
			'NEW PTG',
			'POSTER',	// poster
			'COMBO PACK',	// combo
			' HC ',		// hard cover
			' TP ',		// trade paperback
			' DVD ',	// dvd
			' BD ',		// blue ray
			'BOX SET',	// box set
			'WALL CAL',	// wall calendar
			' SGN',		// signed
			' FIG',		// figurine
			' STATUE',	// statue
			' AF ',		// action figure
			'DIECAST',	// DIECAST metal
			'  T/S ',	// t-shirt
			'T-SHIRT',	// t-shirt
			' TOTE',	// tote
			' CUP',		// cup
			'TRAVEL MUG',	// mug
			'WATER BOTTLE',	// water bottle
			'CERAMIC STEIN',// stein
			'CERAMIC MUG',	// mugs again
			' EXP ',	// expansion
			'MDL KIT',	// model kit

		);
	}

	public function isExcluded( $item = null )
	{
		if (is_null($item) == false) {
			foreach( $this->excludeList() as $excl ) {
				if (contains($excl, $item )) {
					return true;
				}
			}
		}
		return false;
	}


	public function performGET($url, $force = false)
	{
		$this->document = null;
		$this->releaseDate = null;
		$duplicateIndex = array();

		list($data, $headers) = parent::performGET($url, $force);
		if ( empty($data) == true ) {
			throw new \Exception( "No PreviewsWorld data" );
		}

		$this->document = array();
		$dataArray = preg_split('/\n|\r/', $data, -1, PREG_SPLIT_NO_EMPTY);
		$groupList = array_kmap(
			function($k, $v) {
				$split = explode("\t", $v);
				return $split;
			},
			$dataArray
		);

		$currentGroupName = "error";
		foreach( $groupList as $line ) {
			if ( is_array($line) ) {
				if ( count($line) == 1 ) {
					$currentGroupName = $line[0];
					if ( startsWith('New Releases For ', $currentGroupName)) {
						$this->releaseDate = substr($currentGroupName, strlen('New Releases For '));
						$this->releaseDate = strtotime($this->releaseDate);
					}
				}
				else if( in_array($currentGroupName, $this->groupsIgnored()) == false) {
					$code = $line[0];
					$item = $line[1];
					$price = $line[2];
					if ( $price != 'PI' ) {
						if ( $this->isExcluded( $item ) == false ) {
							$item = $this->substitute($item);

							$mediaFilename = new MediaFilename($item);
							$meta = $mediaFilename->updateFileMetaData(null);

							isset($meta['name']) || die ( $line[1] );
							$clean_name = $meta['name'];
							$clean_issue = (isset($meta['issue']) ? $meta['issue'] : null);
							$clean_year = (isset($meta['year']) ? $meta['year'] : null);
							$clean_index = $clean_name . ' | ' . $clean_issue . ' | ' . $clean_year;

							if ( $clean_issue != null && in_array($clean_index, $duplicateIndex) == false) {
								$duplicateIndex[] = $clean_index;
								$this->document[$currentGroupName][] = array(
									'name' => $clean_name,
									'issue' => $clean_issue,
									'year' => $clean_year
								);
							}
						}
					}
				}
			}
			else {
				throw new PreviewsWorldException( "unknown line $line" );
			}
		}

		return array( $this->document, $headers );
	}
}
