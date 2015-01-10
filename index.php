<?php

require 'application/config/config.php';

is_dir(DATA_PATH) || mkdir(DATA_PATH, 0777, true) || die('Failed to create database directory ' . DATA_PATH); 
is_dir(MEDIA_PATH) || mkdir(MEDIA_PATH, 0777, true) || die('Failed to create media directory ' . MEDIA_PATH); 

require 'application/config/autoload.php';
require 'application/config/common.php';
require 'application/config/errors.php';

$app = new Application();

?>