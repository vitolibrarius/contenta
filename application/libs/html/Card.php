<?php
namespace html;

use \Logger as Logger;
use \Cache as Cache;
use \ClassNotFoundException as ClassNotFoundException;
use \DataObject as DataObject;
use \Config as Config;

use \html\Element as H;
use \html\ProgressBar as ProgressBar;

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
		return null;
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

	public function queuedPath()
	{
		if (isset($this->queuedPath)) {
			return Config::Web( $this->queuedPath );
		}
		return null;
	}

	public function setQueuedPath( $path = null )
	{
		$this->queuedPath = $path;
	}

	public function readItemPath()
	{
		if (isset($this->readItemPath)) {
			return Config::Web( $this->readItemPath );
		}
		return null;
	}

	public function setReadItemPath( $path = null )
	{
		$this->readItemPath = $path;
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
			$pk = $record->{$pkAttribute}();
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

	public function render( DataObject $record = null, \Closure $topClosure = null, \Closure $bottomClosure = null )
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
									$c[] = H::p( array( "class" => array("property", $record->tableName(), $key) ), $record->{$keypath}() );
								}
								return (isset($c) ? $c : null);
							},
							function() use($record) {
								if ( $record instanceof \interfaces\ObjectProgress ) {
									$progress = new ProgressBar();
									return $progress->elements($record);
								}
							}
						),

						H::div( array( "class" => "figure_detail_bottom" ),
							function() use($record, $topClosure) {
								$editPath = $this->editPath();
								if (isset($editPath) && is_null($editPath) == false) {
									$c[] = H::a( array("href" => $editPath ),
										H::span( array( "class" => "icon edit"))
									);
								}

								$supportsReadItem = (method_exists($record, "read_date") ? true : false);
								$isReadItem = ($supportsReadItem ? ($record->{"read_date"}() == true) : false );
								$readItemPath = $this->readItemPath();
								if (isset($readItemPath) && is_null($readItemPath) == false && $supportsReadItem == true) {
									$c[] = H::a( array(
												"id" => "a_readable_" . $record->pkValue(),
												"class" => "readable toggle",
												"data-href" => $readItemPath,
												"data-recordId" => $record->pkValue(),
												"href" => "#"
											),
											H::span( array(
												"id" => "span_readable_" . $record->pkValue(),
												"class" => "icon flag " . ($isReadItem?"on":"")
											)
										)
									);
								}

								$flagPath = $this->flagPath();
								if (isset($flagPath) && is_null($flagPath) == false) {
									$c[] = H::a( array(
												"href" => $flagPath
											),
											H::span( array(
												"id" => "span_flag_" . $record->pkValue(),
												"class" => "icon flag"
											)
										)
									);
								}

								$supportsQueue = (method_exists($record, "queued") ? true : false);
								$queued = ($supportsQueue ? ($record->{"queued"}()) : false );
								$queuedPath = $this->queuedPath();
								if (isset($queuedPath) && is_null($queuedPath) == false && $supportsQueue == true) {
// 									if ( $queued instanceof \interfaces\ObjectProgress ) {
// 										$progress = new ProgressBar();
// 										$c[] = $progress->elements($queued);
// 									}

									$c[] = H::div( array("style" => "display: flex;"),
										H::a( array(
												"id" => "a_queued_" . $record->pkValue(),
												"class" => "queued toggle",
												"data-recordId" => $record->pkValue(),
												"data-href" => $queuedPath,
												"href" => "#"
												),
												H::span( array(
													"id" => "span_queued_" . $record->pkValue(),
													"class" => "icon favorite " . ($queued != false ? "on" : "")
												)
											)
										),
										function() use($queued) {
											$progress = new ProgressBar();
											return $progress->elements($queued);
										}
									);
								}

								$supportsWanted = (property_exists($record, "pub_wanted") ? true : false);
								$isWanted = ($supportsWanted ? ($record->{"pub_wanted"} == true) : false );
								$wantedPath = $this->wantedPath();
								if (isset($wantedPath) && is_null($wantedPath) == false && $supportsWanted == true) {
									$c[] = H::a( array(
												"class" => "pub_wanted toggle",
												"data-recordId" => $record->pkValue(),
												"data-href" => $wantedPath,
												"href" => "#"
											),
											H::span( array(
												"id" => "span_wanted_" . $record->pkValue(),
												"class" => "icon star " . ($isWanted?"on":"")
											)
										)
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
							H::div( array( "class" => "actions" ), function() use($topClosure) {
								return (is_null($topClosure) ? null : $topClosure());
							})
						)
					)
			),

			H::figcaption( array("class" => "caption"),
				H::a( array("href" => $this->selectPath()),
					H::em( $record->{$this->displayNameKey()}() )
				),

				H::p( $record->{$this->displayDescriptionKey()}() ),

				H::div( function() use($bottomClosure) {
					return (is_null($bottomClosure) ? null : $bottomClosure());
				})
			)
		);
		return $card->render();
	}
}
