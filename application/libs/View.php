<?php

// guard to ensure basic configuration is loaded
defined('APPLICATION_PATH') || exit("APPLICATION_PATH not found.");

define( 'VIEWS_PATH', APPLICATION_PATH . '/views/' );

use \http\Session as Session;;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;

use html\Element as H;

/**
 * Class View
 *
 * Provides the methods all views will have
 */
class View
{
	function __construct($title = '')
	{
		$this->controllerName = $title;
	}

	public function label()
	{
		$teeth = func_get_args();
		return Localized::Get( $this->controllerName, $teeth );
	}

	/**
	 * get the title of this view using localizable values, unless an override value has been set
	 */
	public function viewTitle()
	{
		if ( isset($this->viewTitle) )  {
			return $this->viewTitle;
		}

		return $this->label( "title" );
	}

	/**
	 * sets the title of this view using non-localizable values.  For example, Publication/issue/user name values
	 */
	public function setViewTitle($key)
	{
		$this->viewTitle = $key;
	}

	public function setLocalizedViewTitle($key = "title")
	{
		$this->viewTitle = $this->label( $key );
	}

	public function addStylesheet($path = null) {
		if ( isset($path) && strlen($path) > 0) {
			$filename = appendPath( '/public/css', implode('_', explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR))));
			$sheet = appendPath(SYSTEM_PATH, $filename);
			if ( file_exists($sheet) ) {
				if ( isset($this->additionalStyles) == false) {
					$this->additionalStyles = array();
				}

				$this->additionalStyles[] = $filename;
			}
		}
	}

	public function addScript($path = null) {
		if ( isset($path) && strlen($path) > 0) {
			$filename = appendPath( '/public/js', implode('_', explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR))));
			$sheet = appendPath(SYSTEM_PATH, $filename);
			if ( file_exists($sheet) ) {
				if ( isset($this->additionalScripts) == false) {
					$this->additionalScripts = array();
				}

				$this->additionalScripts[] = $filename;
			}
		}
	}

	/**
	 * simply includes (=shows) the view. this is done from the controller. In the controller, you usually say
	 * $this->view->render( '/help/index'); to show (in this example) the view index.php in the folder help.
	 * Usually the Class and the method are the same like the view, but sometimes you need to show different views.
	 * @param string $filename Path of the to-be-rendered view, usually folder/file(.php)
	 * @param boolean $render_without_header_and_footer Optional: Set this to true if you don't want to include header and footer
	 */
	public function render($filename, $render_without_header_and_footer = false)
	{
		// page without header and footer, for whatever reason
		if ( file_exists(VIEWS_PATH . $filename . '.php')) {
			header('Content-Type: text/html; charset=utf-8');

			// if filename is "/series/index" this will add custom stylesheets for
			// "/public/css/series.css" and "/public/css/series/index.css"
			$this->addStylesheet( dirname($filename) . '.css' );
			$this->addStylesheet( $filename . '.css' );
			$this->addScript( dirname($filename) . '.js' );
			$this->addScript( $filename . '.js' );
			if ( isset($this->model) ) {
				$this->addStylesheet( "contenta-".$this->model->tableName() . '.css' );
				$this->addScript( "contenta-".$this->model->tableName() . '.js' );
			}

			if ($render_without_header_and_footer == false) {
				require VIEWS_PATH . '_templates/header.php';
			}
// 			echo out the system feedback (error and success messages)
// 			$this->renderFeedbackMessages();
			require VIEWS_PATH . $filename . '.php';
			if ($render_without_header_and_footer == false) {
				require VIEWS_PATH . '_templates/footer.php';
			}
		}
		else {
			Session::addNegativeFeedback("Could not find $filename", $this->controllerName);
			header('location: ' . Config::Web('/error/index'));
		}
	}

	/**
	 * renders the feedback messages into the view
	 */
	public function renderFeedbackMessages($useHTML = true)
	{
		if ( $useHTML == true ) {
			// echo out the feedback messages (errors and success messages etc.),
			// they are in $_SESSION["feedback_positive"] and $_SESSION["feedback_negative"]
			require VIEWS_PATH . '_templates/feedback.php';
		}
		else {
			require VIEWS_PATH . '_templates/feedback_api.php';
		}

		// delete these messages (as they are not needed anymore and we want to avoid to show them twice
		Session::clearAllFeedback();
	}

	/**
	 * renders the feedback messages into the view
	 */
	public function renderEditForm($formName = null)
	{
		if ( isset($formName) && strlen($formName) > 0 ) {
			// echo out the feedback messages (errors and success messages etc.),
			// they are in $_SESSION["feedback_positive"] and $_SESSION["feedback_negative"]
			require VIEWS_PATH . 'edit/' . $formName . '.php';
		}
	}

	public function renderJson($content)
	{
		header('Content-Type: application/json; charset=utf8');
		echo json_encode($content, JSON_PRETTY_PRINT);
	}

	public function render_xml($filename)
	{
		// page without header and footer, for whatever reason
		if ( file_exists(VIEWS_PATH . $filename . '.php')) {
			header('Content-Type: text/xml; charset="utf-8"');
			require VIEWS_PATH . $filename . '.php';
		}
		else {
			header('location: ' . Config::Web('/error/index'));
		}
	}

	/**
	 * Checks if the passed string is the currently active controller.
	 * Useful for handling the navigation's active/non-active link.
	 * @param string $filename
	 * @param string $navigation_controller
	 * @return bool Shows if the controller is used or not
	 */
	private function checkForActiveController($filename, $navigation_controller)
	{
		$split_filename = explode("/", $filename);
		$active_controller = $split_filename[0];

		if ($active_controller == $navigation_controller) {
			return true;
		}
		// default return
		return false;
	}

	/**
	 * Checks if the passed string is the currently active controller-action (=method).
	 * Useful for handling the navigation's active/non-active link.
	 * @param string $filename
	 * @param string $navigation_action
	 * @return bool Shows if the action/method is used or not
	 */
	private function checkForActiveAction($filename, $navigation_action)
	{
		$split_filename = explode("/", $filename);
		$active_action = $split_filename[1];

		if ($active_action == $navigation_action) {
			return true;
		}
		// default return of not true
		return false;
	}

	/**
	 * Checks if the passed string is the currently active controller and controller-action.
	 * Useful for handling the navigation's active/non-active link.
	 * @param string $filename
	 * @param string $navigation_controller_and_action
	 * @return bool
	 */
	private function checkForActiveControllerAndAction($filename, $navigation_controller_and_action)
	{
		$split_filename = explode("/", $filename);
		$active_controller = $split_filename[0];
		$active_action = $split_filename[1];

		$split_filename = explode("/", $navigation_controller_and_action);
		$navigation_controller = $split_filename[0];
		$navigation_action = $split_filename[1];

		if ($active_controller == $navigation_controller AND $active_action == $navigation_action) {
			return true;
		}
		// default return of not true
		return false;
	}
	public function renderPropertyForKeypath( $object, $attr, $keypath )
	{
		if ( isset( $object, $attr, $keypath ) ) {
			$c = H::span( array( "class" => array("property", $object->tableName(), $attr) ), (string)$object->{$keypath}() );
			echo $c->render();
		}
	}

	public function renderPropertyValue( $model, $attr, $value )
	{
		if ( isset( $model, $attr, $value ) ) {
			$c = H::span( array( "class" => array("property", $model->tableName(), $attr) ), (string)$value );
			echo $c->render();
		}
	}

	public function renderFormField( $formType = 'text', $object = null, $type = null, $model, $attr, $value = null, $editable = true, $validation = null)
	{
		$editPanel = VIEWS_PATH . 'edit/' . $formType . 'Component.php';

		$this->input_id = $model->attributeId($attr);
		$this->input_name = $model->attributeName($object, $type, $attr);
		$this->input_label = $model->attributeLabel($object, $type, $attr);
		$this->input_restriction = $model->attributeRestrictionMessage($object, $type, $attr);
		$this->input_pattern = $model->attributeEditPattern($object, $type, $attr);
		$this->input_placeholder = $model->attributePlaceholder($object, $type, $attr);
		$this->input_options = $model->attributeOptions($object, $type, $attr);
		$this->input_value = is_null($value) ?
			$model->attributeDefaultValue($object, $type, $attr) :
			$value;
		$this->input_validation = $validation;

		require $editPanel;
	}

	/**
	 * common label values
	 */
	public function saveButton() { return Localized::GlobalLabel("saveButton"); }
	public function cancelButton() { return Localized::GlobalLabel("cancelButton"); }
	public function deleteButton() { return Localized::GlobalLabel("deleteButton"); }
	public function resetButton() { return Localized::GlobalLabel("resetButton"); }


	function renderImage($defaultImagePath, $imageData, $imageDataMimeType = 'image/png')
	{
		ob_clean();
		if ( isset($imageData) AND ($imageData != false)) {
			$etag = '"'. hash(HASH_DEFAULT_ALGO, $imageData) .'"';

			if ( !empty($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
				header('HTTP/1.1 304 Not Modified');
				header('Content-Length: 0');
				exit;
			}

			$expiry = 604800; // (60*60*24*7)
			header('ETag: ' . $etag);
			header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
			header('Expires:'. gmdate('D, d M Y H:i:s', time() + $expiry) .' GMT');
			header('Content-Type: ' . $imageDataMimeType );
			header('Cache-Control: max-age=86400');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			echo $imageData;
		}
		else if (is_file($defaultImagePath)) {
			$etag = '"'. hash_file(HASH_DEFAULT_ALGO, $defaultImagePath) .'"';
			if ( !empty($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
				header('HTTP/1.1 304 Not Modified');
				header('Content-Length: 0');
				exit;
			}

			$expiry = 604800; // (60*60*24*7)
			header('ETag: ' . $etag);
			header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
			header('Expires:'. gmdate('D, d M Y H:i:s', time() + $expiry) .' GMT');
			header('Content-Type: image/' . file_ext($defaultImagePath) );
			header("Content-Transfer-Encoding: binary");
			header('Cache-Control: max-age=86400');
			header('Pragma: public');
			header('Content-Length: ' . filesize($defaultImagePath));
			readfile($defaultImagePath);
		}
	}
}
