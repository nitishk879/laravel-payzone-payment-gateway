<?php


namespace Svodya\PayZone\includes\helpers;


class DBDemo
{
    //Declare variables and constants for DB access
//    const hostname = config('database.connections.mysql.host') ?? env('DB_HOST');
//    const username = config('database.connections.mysql.username');
//    const password = config('database.connections.mysql.password');
//    const database = config('database.connections.mysql.database');
    const hostname = 'localhost';
    const username = 'root';
    const password = 'ashy';
    const database = 'bomat';
    //set a default response incase of error
    public $response = "An error has occurred";

    /**
     * [__construct ]
     * @method __construct
     */
    public function __construct()
    {

        //magic quotes logic
        if (get_magic_quotes_gpc()) {
            function stripslashes_deep($value)
            {
                $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
                return $value;
            }

            $_POST = array_map('stripslashes_deep', $_POST);
            $_GET = array_map('stripslashes_deep', $_GET);
            $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
            $_REQUEST = array_map('stripslashes_deep', $_REQUEST);

            if (mysqli_connect_errno()) {
                printf("Connect failed: %s\n", mysqli_connect_error());
                exit();
            }
        }
    }

    public static function saveTransaction($order_id, $amounttidy, $amountminor, $amountmajor, $currency, $cross_reference, $status_code, $status, $transaction_datetime, $gateway_message, $integration)
    {

        $timestamp = date('Y-m-d H:i:s', strtotime($transaction_datetime));
        $mysqli = new \mysqli(self::hostname, self::username, self::password, self::database);
        $sqlCheck = "SELECT `id` FROM `transactions` WHERE  `order_id` = '$order_id' AND `cross_reference` = '$cross_reference';";
        $sqlCheck = $mysqli->query($sqlCheck);
        if ($sqlCheck->num_rows == 0) {
            $sqlInsert = "INSERT INTO `transactions` (`id`, `order_id`,`amount_`,`amount_minor`,`amount_major`,`currency`,`cross_reference`,`status_code`,`status`,`transaction_datetime_txt`,`transaction_datetime`,`gateway_message`,`integration_type`) VALUES (NULL, '$order_id','$amounttidy','$amountminor','$amountmajor','$currency','$cross_reference','$status_code','$status','$transaction_datetime','$timestamp','$gateway_message','$integration');";
            $sql = $mysqli->query($sqlInsert);
            return $sql;
        } else {

            return true;
        }
    }

    /**
     * [saveConfiguration Saves config settings into Demo DB, using key value pairs]
     * @method saveConfiguration
     * @param  [String]            $key
     * @param  [String]            $value
     * @return [Boolean]            [Return false if failed]
     */
    public static function saveConfiguration($key, $value)
    {
        $mysqli = new \mysqli(self::hostname, self::username, self::password, self::database);
        $sqlDelete = "DELETE FROM `configurations` WHERE `key` = '$key';";
        $mysqli->query($sqlDelete);
        $sqlInsert = "INSERT INTO `configurations` (`id`, `key`,`value`) VALUES (NULL, '$key','$value');";
        return $mysqli->query($sqlInsert);
    }

    /**
     * [saveConfiguration Saves config settings into Demo DB, using key value pairs]
     * @method saveConfiguration
     * @param  [String]            $key
     * @return [String]            [return value from matches key]
     */
    public static function getConfiguration($key)
    {
        $mysqli = new \mysqli(self::hostname, self::username, self::password, self::database);
        $config = "SELECT `value` FROM `configurations` WHERE `key` = '$key' LIMIT 1;";
        $config = $mysqli->query($config);
        $config = $config->fetch_assoc();
        $config = $config["value"];

        return $config;
    }
}
