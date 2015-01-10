<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

// Set default timezone
date_default_timezone_set('Canada/Mountain');

define('APP_NAME', 'Contenta');

require 'application/config/config-' . gethostname() . '.php';

/**
 * Configuration for: Folders
 * Here you define where your folders are. Unless you have renamed them, there's no need to change this.
 */
define('LIBS_PATH', 'application/libs/');
define('CONTROLLER_PATH', 'application/controllers/');
define('MODELS_PATH', 'application/models/');
define('PROCESSOR_PATH', 'application/processors/');
define('VIEWS_PATH', 'application/views/');

/**
 * http://stackoverflow.com/q/9618217/1114320
 * php.net/manual/en/function.setcookie.php
 */
// 1209600 seconds = 2 weeks
define('COOKIE_RUNTIME', 1209600);
// the domain where the cookie is valid for, for local development ".127.0.0.1" and ".localhost" will work
// IMPORTANT: always put a dot in front of the domain, like ".mydomain.com" !
define('COOKIE_DOMAIN', '.localhost');

/**
 * Configuration for Database
 */
//define('DB_TYPE', 'mysql');
//define('DB_HOST', '127.0.0.1');
//define('DB_NAME', 'login');
//define('DB_USER', 'root');
//define('DB_PASS', 'mysql');

define('DB_TYPE', 'sqlite');
define('DB_PATH', DATA_PATH . DIRECTORY_SEPARATOR . 'database.sqlite');

define("HASH_COST_FACTOR", "10");
define("HASH_DEFAULT_ALGO", "md5");

/**
 *  Some processor constants so only one will run concurrently
 */
define("CharacterUUID", "92FEA05F-309C-45DB-B065-BA0CE1590188");
define("PublisherUUID", "B1B5DBA8-A4E3-435B-90AC-D7293BB7913D");
define("MetadataUUID", "49E360AE-C6B4-4D75-9258-9596E9569A5B");

