<?php

// guard to ensure basic configuration is loaded
defined('APPLICATION_PATH') || exit("APPLICATION_PATH not found.");

define( 'VIEWS_PATH', APPLICATION_PATH . '/views/' );

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

	public function globalLabel( $key, $default = null )
	{
		return Localized::Get( "GLOBAL/" . $key, (is_null($default) ? $key : $default));
	}

	public function localizedLabel( $key, $default = null )
	{
		return Localized::Get( $this->controllerName . "/" . $key, (is_null($default) ? $key : $default));
	}

	/**
	 * get the title of this view using localizable values, unless an override value has been set
	 */
	public function viewTitle()
	{
		if ( isset($this->viewTitle) )  {
			return $this->viewTitle;
		}

		return $this->localizedLabel( "title", $this->controllerName );
	}

	/**
	 * sets the title of this view using non-localizable values.  For example, Publication/issue/user name values
	 */
	public function setViewTitle($key)
	{
		$this->viewTitle = $key;
	}

	public function addStylesheet($path = null) {
		if ( isset($path) && strlen($path) > 0) {
			$filename = appendPath( '/public/css', implode('_', explode('/', trim($path, '/'))));
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
			$filename = appendPath( '/public/js', implode('_', explode('/', trim($path, '/'))));
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
		if ($render_without_header_and_footer == true) {
			require VIEWS_PATH . $filename . '.php';
		} else {
			$current = htmlentities($_SERVER['REQUEST_URI']);
			if ( endsWith($current, '/index') ) {
				Session::clearCurrentPageStack();
			}
			else if ( Session::peekCurrentPage() != $current && count($_POST) == 0 ) {
				Session::pushCurrentPage($current);
			}

			// if filename is "/series/index" this will add custom stylesheets for
			// "/public/css/series.css" and "/public/css/series/index.css"
			$this->addStylesheet( dirname($filename) . '.css' );
			$this->addStylesheet( $filename . '.css' );
			$this->addScript( dirname($filename) . '.js' );
			$this->addScript( $filename . '.js' );

			require VIEWS_PATH . '_templates/header.php';
			require VIEWS_PATH . $filename . '.php';
			require VIEWS_PATH . '_templates/footer.php';
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

/* Form editing support methods
abstract public function attributesFor($object);

abstract public function attributeId($object, $attr);
abstract public function attributeName($object, $attr);

abstract public function attributeIsEditable($object, $attr);
abstract public function attributeLabel($object, $attr);
abstract public function attributeRestrictionMessage($object, $attr);
abstract public function attributeDefaultValue($object, $attr);
abstract public function attributeEditPattern($object, $attr);
abstract public function attributePlaceholder($object, $attr);
*/
	public function renderFormField( $type = 'text', $object, $model, $attr, $submittedValue = null )
	{
		$editPanel = VIEWS_PATH . 'edit/' . $type . 'Component.php';

		$this->input_id = $model->attributeId($object, $attr);
		$this->input_name = $model->attributeName($object, $attr);
		$this->input_label = $model->attributeLabel($object, $attr);
		$this->input_restriction = $model->attributeRestrictionMessage($object, $attr);
		$this->input_pattern = $model->attributeEditPattern($object, $attr);
		$this->input_placeholder = $model->attributePlaceholder($object, $attr);
		$this->input_options = $model->attributeOptions($object, $attr);
		$this->input_value = ($submittedValue == null) ?
			$model->attributeDefaultValue($object, $attr) :
			$submittedValue;

		require $editPanel;
	}

	function splitPOSTValues($array) {
		$ret = array();
		foreach ($array as $key => $value) {
			$components = explode(Model::HTML_ATTR_SEPARATOR, $key);
			if (count($components) > 1) {
				$table = $components[0];
				$attr = $components[1];
				$model = Model::Named($table);
				if ( $model != null ) {
					$type = $model->attributeType(null, $attr);
					switch ($type) {
						case Model::DATE_TYPE:
							$value = strtotime($value);
							break;
						case Model::INT_TYPE:
							$value = intval($value);
							break;
						default:
							break;
					}
				}

				if (isset($ret[$table])) {
					$ret[$table][$attr] = $value;
				}
				else {
					$ret[$table] = array( $attr => $value );
				}
			}
		}
		return $ret;
	}

	/**
	 * common label values
	 */
	public function saveButton() { return $this->globalLabel("saveButton", "Save"); }
	public function cancelButton() { return $this->globalLabel("cancelButton", "Cancel"); }
	public function deleteButton() { return $this->globalLabel("deleteButton", "Delete"); }
}
