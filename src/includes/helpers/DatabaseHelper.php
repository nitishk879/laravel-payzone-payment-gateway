<?php


namespace Svodya\PayZone\includes\helpers;


use App\Configuration;
use App\Transaction;

class DatabaseHelper
{
    public static function saveTransaction($order_id, $amounttidy, $amountminor, $amountmajor, $currency, $cross_reference, $status_code, $status, $transaction_datetime, $gateway_message, $integration)
    {
        $xTrans = Transaction::where('order_id', $order_id)->where('cross_reference', $cross_reference);

        if($xTrans===0){
            return Transaction::create([
                'order_id'    => $order_id,
                'amount_'  => $amounttidy,
                'amount_minor' => $amountminor,
                'amount_major' => $amountmajor,
                'currency' => $currency,
                'cross_reference'  => $cross_reference,
                'status_code'  => $status_code,
                'status'   => $status,
                'transaction_datetime_txt'  => $transaction_datetime,
                'transaction_datetime' => date('Y-m-d H:i:s'),
                'gateway_message'  => $gateway_message,
                'integration_type' => $integration
            ]);
        }else{
            return true;
        }
    }

    public static function saveConfiguration($key, $value)
    {
        $older = Configuration::where('key', $key)->firstOrFail();
        if($older){
            $older->delete();
        }
        return Configuration::create([
            'key'   => $key,
            'value' =>  $value
        ]);
    }

    public static function getConfiguration($key)
    {
        return Configuration::select('value')->where('key', $key)->firstOrFail();
    }

}
