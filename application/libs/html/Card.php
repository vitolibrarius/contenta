<?php
namespace html;

use \Logger as Logger;
use \Cache as Cache;
use \ClassNotFoundException as ClassNotFoundException;
use \DataObject as DataObject;
use \Config as Config;

use html\Element as H;

class Card
{
	public function __construct( )
	{
	}

	/**
	 * Renders the HTML output
	 *
	 * @return   string
	 */
	public function __toString()
	{
		return $this->render();
	}

	public function selectPath()
	{
		if (isset($this->selectPath)) {
			return Config::Web( $this->selectPath );
		}
		return "#";
	}

	public function setSelectPath( $path = null )
	{
		$this->selectPath = $path;
	}

	public function editPath()
	{
		if (isset($this->editPath)) {
			return Config::Web( $this->editPath );
		}
		return null;
	}

	public function setEditPath( $path = null )
	{
		$this->editPath = $path;
	}

	public function deletePath()
	{
		if (isset($this->deletePath)) {
			return Config::Web( $this->deletePath );
		}
		return null;
	}

	public function setDeletePath( $path = null )
	{
		$this->deletePath = $path;
	}

	public function wantedPath()
	{
		if (isset($this->wantedPath)) {
			return Config::Web( $this->wantedPath );
		}
		return "#";
	}

	public function setWantedPath( $path = null )
	{
		$this->wantedPath = $path;
	}

	public function flagPath()
	{
		if (isset($this->flagPath)) {
			return $this->flagPath;
		}
		return null;
	}

	public function favoritePath()
	{
		if (isset($this->favoritePath)) {
			return $this->favoritePath;
		}
		return null;
	}

	public function thumbnailTable(DataObject $record = null)
	{
		if (isset($this->thumbnailTable)) {
			return $this->thumbnailTable;
		}
		return (is_null($record) ? null : $record->tableName());
	}

	public function setThumbnailTable( $path = null )
	{
		$this->thumbnailTable = $path;
	}

	public function thumbnailPrimaryKeypath()
	{
		if (isset($this->thumbnailPrimaryKeypath)) {
			return $this->thumbnailPrimaryKeypath;
		}
		return "id";
	}

	public function setThumbnailPrimaryKeypath( $path = null )
	{
		$this->thumbnailPrimaryKeypath = $path;
	}

	public function thumbnailPath(DataObject $record = null)
	{
		if (isset($record) && is_null($record) == false) {
			$table = $this->thumbnailTable($record);
			$pkAttribute = $this->thumbnailPrimaryKeypath();
			$pk = $record->{$pkAttribute};
			return Config::Web( "Image", "thumbnail", $table, $pk);
		}
		return Config::Web('/public/img/Logo_sm.png');
	}

	public function publisherIconPath($record = null)
	{
		if (isset($record) && is_null($record) == false) {
			$publisher = $record->publisher();
			if ( $publisher != false ) {
				$pk = $publisher->pkValue();
				return Config::Web( "Image", "icon", 'publisher', $pk);
			}
		}
		return Config::Web('/public/img/Logo_favicon.png');
	}

	public function detailKeys()
	{
		if (isset($this->detailKeys)) {
			return $this->detailKeys;
		}
		return array();
	}

	public function setDetailKeys( array $keys = array() )
	{
		return $this->detailKeys = $keys;
	}

	public function displayNameKey()
	{
		if (isset($this->displayNameKey)) {
			return $this->displayNameKey;
		}
		return "displayName";
	}

	public function setDisplayNameKey( $key = null )
	{
		$this->displayNameKey = $key;
	}

	public function displayDescriptionKey()
	{
		if (isset($this->displayDescriptionKey)) {
			return $this->displayDescriptionKey;
		}
		return "displayDescription";
	}

	public function setDisplayDescriptionKey( $key = null )
	{
		$this->displayDescriptionKey = $key;
	}

	public function render( DataObject $record = null, \Closure $callback = null )
	{
		$card = H::figure( array( "class" => "card"),
				H::div( array( "class" => "figure_top" ),
					H::div( array( "class" => "figure_image" ),
						H::a( array("href" => $this->selectPath()),
							H::img( array( "src" => $this->thumbnailPath($record), "class" => "thumbnail recordType" ))
						)
					),
					H::div( array( "class" => "figure_details" ),
						H::div( array( "class" => "figure_detail_top" ),
							H::img( array( "src" => $this->publisherIconPath($record), "class" => "icon publisher" ))
						),

						H::div( array( "class" => "figure_detail_middle" ),
							function() use($record) {
								foreach( $this->detailKeys() as $key => $keypath ) {
									$c[] = H::p( array( "class" => $key ), $record->{$keypath}() );
								}
								return (isset($c) ? $c : null);
							}
						),

						H::div( array( "class" => "figure_detail_bottom" ),
							function() use($record, $callback) {
								$editPath = $this->editPath();
								if (isset($editPath) && is_null($editPath) == false) {
									$c[] = H::a( array("href" => $editPath ),
										H::span( array( "class" => "icon edit"))
									);
								}

								$flagPath = $this->flagPath();
								if (isset($flagPath) && is_null($flagPath) == false) {
									$c[] = H::a( array("href" => $flagPath ),
										H::span( array( "class" => "icon flag"))
									);
								}

								$favoritePath = $this->favoritePath();
								if (isset($favoritePath) && is_null($favoritePath) == false) {
									$c[] = H::a( array("href" => $favoritePath ),
										H::span( array( "class" => "icon favorite"))
									);
								}

								$supportsWanted = (property_exists($record, "pub_wanted") ? true : false);
								$isWanted = ($supportsWanted ? ($record->{"pub_wanted"} == true) : false );
								$wantedPath = $this->wantedPath();
								if (isset($wantedPath) && is_null($wantedPath) == false && $supportsWanted == true) {
									$c[] = H::a( array( "class" => "pub_wanted toggle", "data-href" => $wantedPath, "href" => "#" ),
										H::span( array( "class" => "icon star " . ($isWanted?"on":"")))
									);
								}

								$deletePath = $this->deletePath();
								if (isset($deletePath) && is_null($deletePath) == false) {
									$c[] = H::a( array("href" => "#", "class" => "confirm", "action" => $deletePath),
										H::span( array( "class" => "icon recycle"))
									);
								}
								return (isset($c) ? $c : null);
							},
							H::div( array( "class" => "actions" ), function() use($callback) {
								return (is_null($callback) ? null : $callback());
							})
						)
					)
			),

			H::figcaption( array("class" => "caption"),
				H::a( array("href" => $this->selectPath()),
					H::em( $record->{$this->displayNameKey()}() )
				),

				H::p( $record->{$this->displayDescriptionKey()}() )
			)
		);
		return $card->render();
	}
}
