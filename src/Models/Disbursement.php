<?php

namespace App\Models;

use App\Database\Model;

class Disbursement extends Model {

    protected $fillable = [
        'id',
        'amount',
        'status',
        'timestamp',
        'bank_code',
        'account_number',
        'beneficiary_name',
        'remark',
        'receipt',
        'time_served',
        'fee',
    ];

}
