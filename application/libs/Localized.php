<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

define( 'LANG_PATH', SYSTEM_PATH . '/localization/' );

use \Logger as Logger;
use \Exception as Exception;
use \Metadata as Metadata;

/**
 * Class Localized, for localized user messages
 */
class Localized
{
	private static $instances = array();

	private $metadata = null;
	private $lang = null;

	final public static function instance()
	{
		$lang = 'en';
		if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ) {
			$requestLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
			if ( Localized::HasLanguage($requestLang) == true ) {
				$lang = $requestLang;
			}
		}

		if ( isset(Localized::$instances[$lang]) == false ) {
			Localized::$instances[$lang] = new Localized($lang);
		}

	   return Localized::$instances[$lang];
	}

	public static function HasLanguage($lang = 'en')
	{
		$path = appendPath(LANG_PATH, $lang . ".json" );
		return file_exists($path);
	}

	/**
	 * gets/returns the value of a specific key of the config
	 * @param mixed $key Usually a string, path may be separated using '/', so 'Internet/appname'
	 * @return mixed
	 */
	public static function Get()
	{
		$keys = func_get_args();
		$fullkey = join(DIRECTORY_SEPARATOR, array_flatten($keys));
		return self::instance()->getValue($fullkey);
	}

	public static function GlobalLabel()
	{
		$teeth = func_get_args();
		return Localized::Get( "GLOBAL", $teeth );
	}

	public static function ModelLabel( $table, $attr )
	{
		return Localized::Get("Model", $table, $attr, "label");
	}

	/**
	 * @todo: change this implementation to fall back from specific field message, to general table message to general model message
	 * so Model/users/name/FIELD_EMPTY, could fall back to Model/users/FIELD_EMPTY, to Model/_general_/FIELD_EMPTY
	 * or maybe the keys should be stored in a different order
	 * Model/FIELD_EMPTY/table/attr, or maybe Model/FIELD_EMPTY = "Please provide a value for {attr}"
	 */
	public static function ModelValidation( $table, $attr, $validate = 'validation' )
	{
		return Localized::Get("Model", $table, $attr, $validate );
	}

	public static function ModelRestriction( $table, $attr )
	{
		return Localized::Get("Model", $table, $attr, "restriction");
	}

	public static function ModelSearch( $table, $attr, $search = 'search' )
	{
		return Localized::Get("Model", $table, $attr, $search );
	}

	public function __construct($lang)
	{
		$this->metadata = Metadata::forDirectoryAndFile(LANG_PATH, $lang . ".json", Metadata::TYPE_JSON);
	}

	/**
	 * gets/returns the value of a specific key of the config
	 * @param mixed $key Usually a string, path may be separated using '/', so 'Internet/appname'
	 * @return mixed
	 */
	public function getValue($key)
	{
		if ( Config::Get("Debug/localized") == true && $this->metadata->isMeta($key) == false) {
			$success = $this->metadata->setMeta($key, $key);
		}
		return $this->metadata->getMeta($key);
	}
}
