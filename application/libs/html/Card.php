<?php
namespace html;

use \Logger as Logger;
use \Cache as Cache;
use \ClassNotFoundException as ClassNotFoundException;
use \DataObject as DataObject;
use \Config as Config;

use html\Generator as H;

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

	public function thumbnailPath(DataObject $record = null)
	{
		if (isset($record) && is_null($record) == false) {
			$table = $record->tableName();
			$pk = $record->pkValue();
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

	public function render( DataObject $record = null )
	{
		$this->record = $record;
		H::figure( array( "class" => "card"), function() {
			H::div( array( "class" => "feature" ), function() {
				H::div( array( "class" => "feature_top" ), function() {
					H::div( array( "class" => "feature_top_left" ), function() {
						H::a( array("href" => $this->selectPath()), function() {
							H::img( array( "src" => $this->thumbnailPath($this->record), "class" => "thumbnail recordType" ));
						});
					});
					H::div( array( "class" => "feature_top_right" ), function() {
						H::div( array( "class" => "feature_top_right_top" ), function() {
							H::img( array( "src" => $this->publisherIconPath($this->record), "class" => "icon publisher" ));
						});

						H::div( array( "class" => "feature_top_right_middle" ), function() {
							H::span( array( "class" => "details" ), function() {
								foreach( $this->detailKeys() as $key => $methodName ) {
									$value = '';
									if ( method_exists($this->record, $methodName) ) {
										$value = $this->record->$methodName();
									}
									else if ( isset( $this->record->{$methodName} )) {
										$value = $this->record->{$methodName};
									}
									H::span( array( "class" => $key ), $value );
								}
							});
						});

						H::div( array( "class" => "feature_top_right_bottom" ), function() {
							H::div( array( "class" => "actions" ), function() {

								$editPath = $this->editPath();
								if (isset($editPath) && is_null($editPath) == false) {
									H::a( array("href" => $editPath ), function() {
										H::span( array( "class" => "icon edit"));
									});
								}

								$flagPath = $this->flagPath();
								if (isset($flagPath) && is_null($flagPath) == false) {
									H::a( array("href" => $flagPath ), function() {
										H::span( array( "class" => "icon flag"));
									});
								}

								$favoritePath = $this->favoritePath();
								if (isset($favoritePath) && is_null($favoritePath) == false) {
									H::a( array("href" => $favoritePath ), function() {
										H::span( array( "class" => "icon favorite"));
									});
								}

								$deletePath = $this->deletePath();
								if (isset($deletePath) && is_null($deletePath) == false) {
									H::a( array("href" => "#", "class" => "confirm", "action" => $deletePath), function() {
										H::span( array( "class" => "icon recycle"));
									});
								}
							});
						});
					});
				});
			});

			H::div( array("class" => "clear") );

			H::figcaption( array("class" => "caption"), function() {
				H::a( array("href" => $this->selectPath()), function() {
					$displayName = ($this->record) ? $this->record->displayName() : "Unknown";
					H::em( $displayName );
				});

				$displayDesc = ($this->record) ? $this->record->shortDescription() : "Short Description";
				H::p( $displayDesc );
			});
		});
		unset($this->record);
	}
}
