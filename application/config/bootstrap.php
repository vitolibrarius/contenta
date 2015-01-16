<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");


error_reporting(E_ALL);
ini_set("display_errors", 1);

// Set default timezone
date_default_timezone_set('Canada/Mountain');


/**
 * Configuration for: Folders
 * Here you define where your folders are. Unless you have renamed them, there's no need to change this.
 */
define('LIBS_PATH', 		'application/libs/');
define('CONTROLLER_PATH',	'application/controllers/');
define('MODELS_PATH',		'application/models/');
define('PROCESSOR_PATH',	'application/processors/');
define('VIEWS_PATH',		'application/views/');

/**
 * http://stackoverflow.com/q/9618217/1114320
 * php.net/manual/en/function.setcookie.php
 */
// 1209600 seconds = 2 weeks
define('COOKIE_RUNTIME', 1209600);
// the domain where the cookie is valid for, for local development ".127.0.0.1" and ".localhost" will work
// IMPORTANT: always put a dot in front of the domain, like ".mydomain.com" !
define('COOKIE_DOMAIN', '.localhost');

define("HASH_COST_FACTOR", "10");
define("HASH_DEFAULT_ALGO", "md5");
