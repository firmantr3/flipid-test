<?php

use App\Database\Migration;

$app = require('bootstrap/app.php');

$migration = (new Migration)->setDriver($app->database);

$migration->addMigration(
    'Create disbursement table',
    implode(' ', [
        "CREATE TABLE `disbursement` (",
        "`id` bigint(20) UNSIGNED NOT NULL,",
        "`amount` int(11) UNSIGNED NOT NULL,",
        "`status` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,",
        "`timestamp` datetime NOT NULL,",
        "`bank_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,",
        "`account_number` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,",
        "`beneficiary_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,",
        "`remark` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,",
        "`receipt` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,",
        "`time_served` datetime NOT NULL,",
        "`fee` int(11) UNSIGNED NOT NULL",
        ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;",
    ])
);

$migration->addMigration(
    'Add primary key to disbursement table',
    implode(' ', [
        'ALTER TABLE `disbursement`',
        'ADD PRIMARY KEY (`id`);',
    ])
);

$result = $migration->run();

echo implode(PHP_EOL, $result);
