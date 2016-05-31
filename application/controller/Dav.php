<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \http\Session as Session;;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \SimpleXMLElement as SimpleXMLElement;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use \model\user\Users as Users;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series as Series;
use model\Series_Alias as Series_Alias;
use model\Series_Character as Series_Character;
use model\User_Series as User_Series;

use webdav\DavNavCollection as DavNavCollection;
use webdav\DavModelCollection as DavModelCollection;
use webdav\DavModelFile as DavModelFile;

class Dav extends Controller
{
	public function __call($method, $args)
	{
        $req_method  = strtolower($_SERVER["REQUEST_METHOD"]);
        $action_method = 'dav_' . $req_method;
// 		Logger::logInfo( "WebDav method '".$_SERVER["REQUEST_METHOD"]."'" );

		$resourcePath = rtrim($_SERVER["REQUEST_URI"], '/');
		$web_dir = Config::Web("Dav");
		if ( null !== $web_dir ) {
			$resourcePath = substr($resourcePath, strlen($web_dir));
		}

        if (empty($resourcePath)) {
            $resourcePath = "/";
        }

        if ( $req_method != 'options' || $resourcePath != '/' ) {
			$auth_type = isset($_SERVER["AUTH_TYPE"]) ? $_SERVER["AUTH_TYPE"] : null;
	        $auth_user = isset($_SERVER["PHP_AUTH_USER"]) ? $_SERVER["PHP_AUTH_USER"] : null;
	        $auth_pw   = isset($_SERVER["PHP_AUTH_PW"]) ? $_SERVER["PHP_AUTH_PW"] : null;

			if ( Auth::httpAuthenticate($auth_type, $auth_user, $auth_pw) == false ) {
				header('WWW-Authenticate: Basic realm="'.(Config::AppName()).' WebDav"');
				http_response_code(401); // Unauthorized
				return;
			}
        }

        if (method_exists($this, $action_method)) {
            $this->$action_method($resourcePath);
        }
        else {
            if ($req_method == "lock") {
				http_response_code(412); // Precondition failed
            }
            else {
            	Logger::logError( "WebDav method not implemented '".$_SERVER["REQUEST_METHOD"]."'" );
				http_response_code(405); // Method not allowed
                header("Allow: " . implode(", ", $this->allowedMethods()));
            }
        }
    }

	function resourcesFor($resourcePath = null, $depth = 0)
	{
		$resources = array();
		if ( empty( $resourcePath) || $resourcePath == "/" ) {
			$resources[] = new DavNavCollection( "" );
			if ( $depth != 0 ) {
				$publishers = Model::Named("Publisher")->allObjects(null, -1);
				foreach( $publishers as $publish ) {
					$resources[] = new DavModelCollection($publish);
				}
			}
		}
		else {
			$path = array_filter( explode('/', $resourcePath), 'strlen');
			$pathSize = count($path);
			$lastName = array_pop( $path );
			$lastNameComponents = explode("_", $lastName);
			$id = array_shift($lastNameComponents);

			$model = null;
			$resources = array();
			switch( $pathSize ) {
				case 1: $model = Model::Named( "Publisher" );
					$obj = $model->objectForId( $id );
					if ( $obj != false ) {
						$root = new DavModelCollection($obj, true);
						if ( $depth == 0 ) {
							$resources[] = $root;
						}
						else if ( $depth != 0 ) {
							$children = $root->getChildren();
							$resources = array_merge($resources, $children);
						}
					}
					break;
				case 2: $model = Model::Named( "Series" );
					$obj = $model->objectForId( $id );
					if ( $obj != false ) {
						$root = new DavModelCollection($obj, true);
						if ( $depth == 0 ) {
							$resources[] = $root;
						}
						else if ( $depth != 0 ) {
							$children = $root->getChildren();
							$resources = array_merge($resources, $children);
						}
					}
					break;
				case 3: $model = Model::Named( "Publication" );
					$obj = $model->objectForId( $id );
					if ( $obj != false ) {
						$root = new DavModelFile($obj, true);
						$resources[] = $root;
					}
					break;
				default: break;
			}
		}
		return $resources;
	}

    function allowedMethods()
    {
        return array("OPTIONS", "PROPFIND", "MKCOL", "GET");
    }

    function dav_options()
    {
        // get allowed methods
        $allowed = $this->allowedMethods();

		// set the DAV compliance level, I don't see a need for 'lock' right now, but who knows
        $davCompliant = array(1);
        if (isset($allowed['LOCK'])) {
            $davCompliant[] = 2;
        }

        header( "DAV: " . implode(", ", $davCompliant));
        header( "Allow: " . implode(", ", $allowed));
        header( "Content-length: 0");
    }

	function dav_propfind($resourcePath = null)
	{
		$depth = "infinity";
        if (isset($_SERVER['HTTP_DEPTH'])) {
            $depth = $_SERVER["HTTP_DEPTH"];
        }

		$resources = $this->resourcesFor( $resourcePath, $depth );
		$href = Config::Web( "Dav", $resourcePath );

		try {
	        $xml = $this->parseXMLRequest("php://input");
// 	        $xml = $this->parseXMLRequest("/tmp/webdav_propfind.xml");
			$propsRequested = array();
			if ( is_null($xml) ) {
				$propsRequested = array(
					array( "getcontentlength", "D:getcontentlength", "" ),
					array( "getcontenttype", "D:getcontenttype", "" ),
					array( "creationdate", "D:creationdate", "" ),
					array( "getlastmodified", "D:getlastmodified", "" ),
					array( "displayname", "D:displayname", "" ),
					array( "resourcetype", "D:resourcetype", "" ),
					array( "checked-in", "D:checked-in", "" ),
					array( "checked-out", "D:checked-out", "" ),
					array( "lockdiscovery", "D:lockdiscovery", ""),
					array( "supportedlock", "D:supportedlock", "")
				);
			}
			else if ( $xml instanceof SimpleXMLElement ) {
				$prop = $xml->children("DAV:");
				foreach( $prop as $propNode ) {
					$nsArray = $propNode->getNamespaces(true);
					$propList = array();
					foreach( $nsArray as $ns ) {
						$children = $propNode->children($ns);
						foreach( $children as $omg ) {
							$propList[] = $omg;
						}
					}
					foreach( $propList as $child ) {
						$propName = $child->getName();
						$nodename = "D:" . $propName;
						$nsString = '';
						$ns = $child->getNamespaces();
						if ( count($ns) > 0 ) {
							$nsuri = array_pop($ns);
							if ( $nsuri != "DAV:" ) {
								$nodename = $propName;
								$nsString = " xmlns=\"".$nsuri."\"";
							}
						}

						$propsRequested[] = array( $propName, $nodename, $nsString );
					}
				}
			}
			$this->view->props_requested = $propsRequested;
			$this->view->href = $href;
			$this->view->resources = $resources;

			http_response_code(207); // Multi-Status

			$this->view->render_xml( '/dav/propfind');

// ob_start();
// 			$this->view->render_xml( '/dav/propfind' );
// $xml = ob_get_clean();
//
// 			file_put_contents('/tmp/webdav_propfind_' . sanitize($resourcePath) . "-" . $depth . '.txt', $xml);
// 			echo $xml;
		}
		catch ( \Exception $e ) {
			Logger::logException( $e );
			http_response_code(400); // Error
		}
	}

    function dav_mkcol($resourcePath = null)
    {
    	return;
    }

    function dav_get($resourcePath = null)
    {
		$resources = $this->resourcesFor( $resourcePath, 0 );
		if ( is_array($resources) && count($resources) == 1 ) {
			$mediaObj = $resources[0]->media();
			if ( $mediaObj != false ) {
				$path = $mediaObj->contentaPath();
				$etag = $mediaObj->checksum;

				if ( file_exists($path) == true )
				{
// 					$user->flagMediaAsRead($mediaObj);
					if ( !empty($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
						header('HTTP/1.1 304 Not Modified');
						header('Content-Length: 0');
						exit;
					}

					$expiry = 604800; // (60*60*24*7)
					header('ETag: ' . $etag);
					header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
					header('Expires:'. gmdate('D, d M Y H:i:s', time() + $expiry) .' GMT');
					header('Content-Description: File Transfer');
					header('Content-Type: application/x-cbz');
					header('Content-Disposition: attachment; filename=' . basename($path));
					header('Pragma: public');
					header('Content-Length: ' . filesize($path));
					ob_clean();
					flush();
					readfile($path);
				}
				else {
					Logger::logError( "Failed to find file for $resourcePath" );
					http_response_code(400); // Error
				}
			}
			else {
				Logger::logError( "Failed to find media for $resourcePath" );
				http_response_code(400); // Error
			}
		}
		else {
			Logger::logError( "Failed to find resource for $resourcePath" );
			http_response_code(400); // Error
		}
    }

	function parseXMLRequest($file)
	{
		$rawData = file_get_contents($file);
		if ( strlen($rawData) == 0 ) {
			return null;
		}

		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($rawData);
		$xmlErrors = libxml_get_errors();
		libxml_clear_errors();

		if ( is_a($xml, 'SimpleXMLElement') == false )
		{
			if (is_array($xmlErrors) && count($xmlErrors) > 0) {
				foreach( $xmlErrors as $idx => $xmle) {
					Logger::logError( "XML parse error $idx " . var_export($xmle, true));
				}
				throw new \Exception($xmlErrors[0]->message);
			}
			else {
				throw new \Exception("Error parsing XML " . var_export($xml, true));
			}
		}
		return $xml;
	}
}
