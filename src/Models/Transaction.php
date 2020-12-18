<?php

namespace Svodya\PayZone\models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['order_id', 'amount_', 'amount_minor', 'amount_major', 'currency', 'cross_reference', 'status_code', 'status', 'gateway_message', 'transaction_datetime', 'transaction_datetime_txt', 'integration_type'];

}
