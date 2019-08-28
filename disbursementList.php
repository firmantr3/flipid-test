<?php

use App\Core;
use App\Models\Disbursement;

/** @var Core $app */
$app = require("bootstrap/app.php");

$page = (int) $app->getArgument(1, 1);
$pageLimit = 2;

$disbursementsQuery = (new Disbursement)->setDriver($app->database)
    ->orderBy('timestamp', 'desc');

$disbursementsCount = $disbursementsQuery->count();
$pagesCount = ceil($disbursementsCount / $pageLimit);
$offset = ($page - 1) * $pagesCount;

$disbursements = $disbursementsQuery->skip($offset)
    ->take($pageLimit)
    ->get();

echo "Showing page {$page} of {$pagesCount}" . PHP_EOL;
echo PHP_EOL;

print_r($disbursements);
