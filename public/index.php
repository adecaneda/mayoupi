<?php

error_reporting(E_ALL);

// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../src'));

require __DIR__.'/../vendor/autoload.php';

$app = new Library\Application();
$app->bootstrap()->run();