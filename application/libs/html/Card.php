<?php
namespace html;

use \Logger as Logger;
use \Cache as Cache;
use \ClassNotFoundException as ClassNotFoundException;
use \DataObject as DataObject;
use \Config as Config;


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
			$table = $record->tableName();
			$pk = $record->pkValue();
			return Config::Web( "Image", "icon", $table, $pk);
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

	public function render(DataObject $record = NULL)
	{
		$card = '<figure class="card"><div class="feature">';

		$card .= '<div class="feature_top">';

			$card .= '<div class="feature_top_left">';
			$card .= '<a href="' . $this->selectPath() . '" ><img src="' . $this->thumbnailPath($record) . '" class="thumbnail recordType" /></a>';
			$card .= '</div>';

			$card .= '<div class="feature_top_right">';
				$card .= '<div class="feature_top_right_top"><img src="' . $this->publisherIconPath($record) . '" class="icon publisher" /></div>';

				$card .= '<div class="feature_top_right_middle">';
				$card .= '<span class="details">';
				foreach( $this->detailKeys() as $key => $methodName ) {
					$value = '';
					if ( method_exists($record, $methodName) ) {
						$value = $record->$methodName();
					}
					else if ( isset( $record->{$methodName} )) {
						$value = $record->{$methodName};
					}
					$card .= '<span class="' . $key . '">' . $value . '</span>';
				}
				$card .= '</span>';
				$card .= '</div>';

				$card .= '<div class="feature_top_right_bottom">';
				$card .= '<div class="actions">';
					$editPath = $this->editPath();
					if (isset($editPath) && is_null($editPath) == false) {
						$card .= ' <a href="' . $editPath . '"><span class="icon edit" /></a>';
					}

					$flagPath = $this->flagPath();
					if (isset($flagPath) && is_null($flagPath) == false) {
						$card .= ' <a href="' . $flagPath . '"><span class="icon flag" /></a>';
					}

					$favoritePath = $this->favoritePath();
					if (isset($favoritePath) && is_null($favoritePath) == false) {
						$card .= ' <a href="' . $favoritePath . '"><span class="icon favorite" /></a>';
					}

					$deletePath = $this->deletePath();
					if (isset($deletePath) && is_null($deletePath) == false) {
						$card .= ' <a class="confirm" href="#" action="' . $deletePath . '"><span class="icon recycle" /></a>';
					}
				$card .= '</div>';
				$card .= '</div>';

			$card .= '</div>';
		$card .= '</div>';
		$card .= '</div><div class="clear"></div>';

		$card .= '<figcaption class="caption"><a href="' . $this->selectPath() . '"><em>';
		$card .= ((isset($record) && is_null($record) == false) ? $record->displayName() : "Unknwown");
		$card .= '</em></a><br />';
		$card .= ((isset($record) && is_null($record) == false) ? $record->displayDescription() : "Short Description");
		$card .= '</figcaption>';
		$card .= '</figure>';

		return $card;
	}
}
