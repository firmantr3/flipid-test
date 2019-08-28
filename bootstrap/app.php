<?php

use App\Core;

$startTime = microtime(true);

ob_start();

// autoloader by composer
require("vendor/autoload.php");

$path = realpath(__DIR__ . '/../');

// load helper
require("{$path}/src/helpers.php");

// initilize app
$app = (new Core)->setPath($path)
    ->setStartTime($startTime);

return $app;
