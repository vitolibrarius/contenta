<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");


error_reporting(E_ALL);
ini_set("display_errors", 1);

// Set default timezone
date_default_timezone_set('Canada/Mountain');

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
define("HASH_DEFAULT_ALGO", "sha256"); // md5?

define("KILOBYTE", 1024);
define("MEGABYTE", 1024 * 1024);
define("GIGABYTE", 1024 * 1024 * 1024);

$_utf8_specials = array (
	"UTF8_PENCIL" => '\u270E',
	"UTF8_BL_HEART" => '\u2665',
	"UTF8_WH_FLAG" => '\u2690',
	"UTF8_BL_FLAG" => '\u2691',
	"UTF8_STAR" => '\u2605',
	"UTF8_FINGER" => '\u261e',
	"UTF8_MATH_NULL" => '\u29b0',
	"UTF8_TRASH" => '\u2425',
	"UTF8_RECYCLE" => '\u2672',
	"UTF8_CHECK" => '\u2713',
	"UTF8_BALLOTX" => '\u2717',
	"UTF8_RETRY" => '\u21BA'
);

foreach( $_utf8_specials as $name => $special ) {
	define( $name, json_decode('"'. $special .'"'));
}
