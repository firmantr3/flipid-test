<?php

use App\Core;
use App\Models\Disbursement;

/** @var Core $app */
$app = require("bootstrap/app.php");

$disbursementQuery = (new Disbursement)->setDriver($app->database);

$latestDisbursement = $disbursementQuery->orderBy('timestamp', 'desc')
    ->take(1)
    ->get();

$latestDisbursementId = $latestDisbursement[0]['id'];

$transactionId = $app->readInput("Transaction ID? [{$latestDisbursementId}]" . PHP_EOL, $latestDisbursementId);

$result = $app->api->showDisbursement([
    'id' => $transactionId,
]);

$updateDisbursement = $disbursementQuery->where([
        'id' => $transactionId,
    ])
    ->update([
        'status' => $result['status'],
        'receipt' => $result['receipt'],
        'time_served' => $result['time_served'],
    ]);

print_r($result);
