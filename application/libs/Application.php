<?php

use \Logger as Logger;
use \Exception as Exception;
use \ClassNotFoundException as ClassNotFoundException;

use utilities\Stopwatch as Stopwatch;

/**
 * Class Application
 * The heart of the app
 */
class Application
{
	/** @var null The controller part of the URL */
	private $url_controller;
	/** @var null The method part (of the above controller) of the URL */
	private $url_action;
	/** @var null Parameter one of the URL */
	private $url_parameter_1;
	/** @var null Parameter two of the URL */
	private $url_parameter_2;
	/** @var null Parameter three of the URL */
	private $url_parameter_3;

	/**
	 * Starts the Application
	 * Takes the parts of the URL and loads the according controller & method and passes the parameter arguments to it
	 * TODO: get rid of deep if/else nesting
	 * TODO: make the hardcoded locations ("error/index", "index.php", new Index()) dynamic, maybe via config.php
	 */
	public function __construct()
	{
		$this->splitUrl();

		if ($this->url_controller) {
			$traceName = $this->url_controller . '/' . (isset($this->url_action) ? $this->url_action : 'index');
			$traceParam = '';
			if (isset($this->url_parameter_3)) {
				$traceParam .= $this->url_parameter_1 . '/' . $this->url_parameter_2 . '/' . $this->url_parameter_3;
			}
			elseif (isset($this->url_parameter_2)) {
				$traceParam .= $this->url_parameter_1 . '/' . $this->url_parameter_2;
			}
			elseif (isset($this->url_parameter_1)) {
				$traceParam .= $this->url_parameter_1;
			}

			Stopwatch::start( $traceName );
			Logger::instance()->setTrace( $traceName, $traceParam );

			try {
				$controllerClass = "controller\\" . $this->url_controller;
				$this->url_controller = new $controllerClass();

				if ($this->url_action) {
					if (method_exists($this->url_controller, $this->url_action)) {
						// call the method and pass the arguments to it
						if (isset($this->url_parameter_3)) {
							$this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2, $this->url_parameter_3);
						}
						elseif (isset($this->url_parameter_2)) {
							$this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2);
						}
						elseif (isset($this->url_parameter_1)) {
							$this->url_controller->{$this->url_action}($this->url_parameter_1);
						}
						else {
							// if no parameters given, just call the method without arguments
							$this->url_controller->{$this->url_action}();
						}
					}
					else if ( is_a($this->url_controller, "controller\\Dav") ) {
						if (isset($this->url_parameter_3)) {
							$this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2, $this->url_parameter_3);
						}
						elseif (isset($this->url_parameter_2)) {
							$this->url_controller->{$this->url_action}($this->url_parameter_1, $this->url_parameter_2);
						}
						elseif (isset($this->url_parameter_1)) {
							$this->url_controller->{$this->url_action}($this->url_parameter_1);
						}
						else {
							// if no parameters given, just call the method without arguments
							$this->url_controller->{$this->url_action}();
						}
					}
					else {
						Session::addNegativeFeedback( "Unknown URL action " . $this->url_action );
						Logger::logError( "Controller " . $controllerClass . " does not implement " . $this->url_action,
							get_class($this), null);
						// redirect user to error page (there's a controller for that)
						header('location: ' . Config::Web('/error/index'));
					}
				}
				else {
					// default/fallback: call the index() method of a selected controller
					$this->url_controller->index();
				}
			}
			catch ( ClassNotFoundException $exception ) {
				Logger::logException( $exception );
				header('location: ' . Config::Web('/error/index'));
			}
			catch ( Exception $e ) {
				Logger::logException( $e );
				header('location: ' . Config::Web('/error/index'));
			}

			$elapsed = Stopwatch::elapsed( $traceName );
			if ( $elapsed > 5.0 ) {
				Logger::logWarning( "Slow page response $elapsed seconds" );
			}
		}
		else {
			// invalid URL, so simply show home/index
			$controller = new controller\Index();
			$controller->index();
		}
	}

	/**
	 * Gets and splits the URL
	 */
	private function splitUrl()
	{
		if (isset($_GET['url'])) {
			$url = rtrim($_GET['url'], '/');

			// remove the Config::Web() prefix
			$web_dir = Config::Web();
			if ( null !== $web_dir )
			{
				if (substr($url, 0, strlen($web_dir)) == $web_dir)
				{
					$url = substr($url, strlen($web_dir));
				}
			}

			// split URL
			$url = filter_var($url, FILTER_SANITIZE_URL);
			$url = explode('/', $url);

			$this->url_controller = (isset($url[0]) ? ucwords($url[0]) : null);
			$this->url_action = (isset($url[1]) ? $url[1] : null);
			$this->url_parameter_1 = (isset($url[2]) ? $url[2] : null);
			$this->url_parameter_2 = (isset($url[3]) ? $url[3] : null);
			$this->url_parameter_3 = (isset($url[4]) ? $url[4] : null);
		}
	}

	public function debug() {
		echo '<div class="debug-helper-box">';

		print_r( '<br/>$_GET[url]  ' . $_GET['url'] );

		echo '</div>';
	}
}
