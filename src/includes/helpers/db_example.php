<?php /**
* Payzone Payment Gateway
* ========================================
* Web:   http://payzone.co.uk
* Email:  online@payzone.com
* Authors: Payzone, Keith Rigby
*/
namespace Payzone\Helpers\DBControl;

if(count(get_included_files()) ==1) exit("Direct access not permitted.");
?>
<?php

##### DEVELOPER NOTICE #####
/*
This class is dependent on two tables being created, one for the configuration and on for te transaction log, you can create these tables your self or use the below SQL statements to create dummy versions.

The configuration table would be best suited to being stored in your CMS / eCommerce sites configuration table to avoid duplcation of effort, similarly the transaction log would be best connecting to the existing transaction log if this exists.

~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
CREATE TABLE `configuration` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(50) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52610 DEFAULT CHARSET=latin1;
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
CREATE TABLE `transaction_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `amount_` varchar(50) DEFAULT NULL,
  `amount_minor` decimal(20,0) DEFAULT NULL,
  `amount_major` float DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `cross_reference` varchar(30) DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL,
  `status` varchar(40) DEFAULT NULL,
  `gateway_message` varchar(255) DEFAULT NULL,
  `transaction_datetime` datetime DEFAULT NULL,
  `transaction_datetime_txt` varchar(100) DEFAULT NULL,
  `integration_type` varchar(50) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
*/
############################
/**
 * [DBDemo Example SQL logging class, for saving configuration and transaction log]
 */
class DBDemo
{
  //Declare variables and constants for DB access
  const hostname = "localhost";
  const username = "username";
  const password = "password";
  const database = "database";
  //set a default response incase of error
  public  $response = "An error has occurred";

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
  /**
   * [saveTransaction Saves transaction log details to the DB]
   * @method saveTransaction
   * @param  [String]            $order_id
   * @param  [String]            $amounttidy
   * @param  [Int]               $amountminor
   * @param  [Float]             $amountmajor
   * @param  [Int]               $currency_code
   * @param  [String]            $crossreference
   * @param  [String]            $statuscode
   * @param  [String]            $type
   * @param  [String]            $transactiondatetime
   * @param  [String]            $message
   * @param  [String]            $integration
   * @return [Boolean]           [complete or failed]
   */
  public static function saveTransaction($order_id,$amounttidy,$amountminor,$amountmajor,$currency,$cross_reference,$status_code,$status,$transaction_datetime,$gateway_message,$integration)
  {

    $timestamp = date('Y-m-d H:i:s',strtotime($transaction_datetime));
    $mysqli = new \mysqli(self::hostname, self::username, self::password, self::database);
    $sqlCheck = "SELECT `id` FROM `transaction_log` WHERE  `order_id` = '$order_id' AND `cross_reference` = '$cross_reference';";
    $sqlCheck = $mysqli->query($sqlCheck);
    if ($sqlCheck->num_rows==0){
      $sqlInsert = "INSERT INTO `transaction_log` (`id`, `order_id`,`amount_`,`amount_minor`,`amount_major`,`currency`,`cross_reference`,`status_code`,`status`,`transaction_datetime_txt`,`transaction_datetime`,`gateway_message`,`integration_type`) VALUES (NULL, '$order_id','$amounttidy','$amountminor','$amountmajor','$currency','$cross_reference','$status_code','$status','$transaction_datetime','$timestamp','$gateway_message','$integration');";
      $sql =  $mysqli->query($sqlInsert);
      return $sql;
    }
    else
    {

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
  public static function saveConfiguration($key,$value)
  {
    $mysqli = new \mysqli(self::hostname, self::username, self::password, self::database);
    $sqlDelete= "DELETE FROM `configuration` WHERE `key` = '$key';";
    $mysqli->query($sqlDelete);
    $sqlInsert = "INSERT INTO `configuration` (`id`, `key`,`value`) VALUES (NULL, '$key','$value');";
    return  $mysqli->query($sqlInsert);
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
    $config = "SELECT `value` FROM `configuration` WHERE `key` = '$key' LIMIT 1;";
    $config = $mysqli->query($config);
    $config = $config->fetch_assoc();
    $config=$config["value"];


    return  $config;
  }


}

?>
