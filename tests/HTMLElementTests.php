<?php

	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
	}

	define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
	define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

	require SYSTEM_PATH .'application/config/bootstrap.php';
	require SYSTEM_PATH .'application/config/autoload.php';
	require SYSTEM_PATH .'application/config/common.php';
	require SYSTEM_PATH .'application/config/errors.php';
	require SYSTEM_PATH .'application/libs/Config.php';
	require SYSTEM_PATH .'application/libs/Cache.php';

	require SYSTEM_PATH .'tests/_ResetConfig.php';
	require SYSTEM_PATH .'tests/_Data.php';

use html\Element as H;

$html = H::tag( 'html' );
$head = $html->addElement( 'head' );
$body = $html->addElement('body')->addClass("main")->setAttribute( "width", "100%" );

$html->render();

echo PHP_EOL;

$list = array( "a" => "this/way", "b" => "none", "c" => __file__ );
$html = H::figure( array( "class" => "card"),
	H::div( array( "class" => "feature" ),
		H::div( array( "class" => "feature_top" ),
			H::div( array( "class" => "feature_top_left" ), function() use ($list) {
					foreach( $list as $key => $keypath ) {
						$c[] = H::span( array( "class" => $key ), $keypath );
					}
					return (isset($c) ? $c : null);
				}
			)
		)
	),
	H::div( array( "class" => "feature" ),
		H::div( array( "class" => "feature_top" ),
			H::div( array( "class" => "feature_top_left" ))
		)
	)
);
$html->render();

echo PHP_EOL;
