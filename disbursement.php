<?php 

use App\Core;
use App\Models\Disbursement;

/** @var Core $app */
$app = require("bootstrap/app.php");

$result = $app->api->createDisbursement([
    'bank_code' => $app->readInput('Bank code? [bni] ' . PHP_EOL, 'bni'),
    'account_number' => $app->readInput("Account number? [1234567890]" . PHP_EOL, '1234567890'),
    'amount' => $app->readInput("Amount? [10000]" . PHP_EOL, '10000'),
    'remark' => $app->readInput("Remark? [sample remark]" . PHP_EOL, 'sample remark'),
]);

$newDisbursement = (new Disbursement)->setDriver($app->database)
    ->insert($result);

print_r($result);
