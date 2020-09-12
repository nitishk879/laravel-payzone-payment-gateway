<?php /**
* Payzone Payment Gateway
* ========================================
* Web:   http://payzone.co.uk
* Email:  online@payzone.com
* Authors: Payzone, Keith Rigby
*/
namespace Payzone\Helper;

if (count(get_included_files()) ==1) {
  exit("Direct access not permitted.");
}

require_once(__DIR__ . "/../gateway/common.php");
require_once(__DIR__ .'/iso_currencies.php');
require_once(__DIR__ .'/iso_countries.php');

use Payzone\Helpers\ISO as ISO;
/**
 * [PayzoneHelper Main payzone payment helper class, for handling strings, variables and array, hash functions and validations along with results retrieval and gateway communication ]
 */
class PayzoneHelper
{
  /**
   * [$trTransactionResult Transaction result object]
   * @var [Object]
   */
  public static $trTransactionResult;


/**
 * [boolToString Convert Boolean values to Strings]
 * @method boolToString
 * @param  [Boolean]       $boBool
 * @return [String]       [String value of Boolean]
 */
  public static function boolToString($boBool)
  {
    $ReturnValue = "false";
    if ($boBool) {
      $ReturnValue = "true";
    }
    return ($ReturnValue);
  }

/**
 * [stringToBool Convert String Value to boolean]
 * @method stringToBool
 * @param  [String]       $String
 * @return [Boolean]       [Boolean value of string]
 */
  public static function stringToBool($String)
  {
    $boReturnValue = false;
    if (strToUpper($String) == "TRUE") {
      $boReturnValue = true;
    }
    return ($boReturnValue);
  }
/**
 * [getSiteSecureURL Retrieve site URL]
 * @method getSiteSecureURL
 * @param  [String]           $type [variable to select type to return]
 * @return [String]          [URL for Base site or Root Site]
 */
  public static function getSiteSecureURL($type)
  {
    $ReturnString = "";
    $PortString = "";
    $ProtocolString = "";
    if (isset($_SERVER["HTTPS"])) {
      $ProtocolString = "https://";
      if ($_SERVER["SERVER_PORT"] != 443) {
        $PortString = ":" . $_SERVER["SERVER_PORT"];
      }
    }
    else {
      $ProtocolString = "http://";
      if ($_SERVER["SERVER_PORT"] != 80) {
        $PortString = ":" . $_SERVER["SERVER_PORT"];
      }
    }

    switch ($type) {
      case 'root':
        $ReturnString = $ProtocolString . $_SERVER["SERVER_NAME"] . $PortString;
        break;
      case 'base':
        $ReturnString = $ProtocolString . $_SERVER["SERVER_NAME"] . $PortString . $_SERVER["SCRIPT_NAME"];
        $boFinished = false;
        $LoopIndex = strlen($ReturnString) - 1;
        while ($boFinished == false && $LoopIndex >= 0) {
          if ($ReturnString[$LoopIndex] == "/") {
            $boFinished = true;
            $ReturnString = substr($ReturnString, 0, $LoopIndex + 1);
          }
          $LoopIndex--;
        }
        break;
      default:
        break;
    }
    return ($ReturnString);
  }
  /**
   * [addStringToStringList Add strings to string list for errors / messages]
   * @method addStringToStringList
   * @param  [String]                $ExistingStringList [Existing String]
   * @param  [String]                $StringToAdd        [New string to append]
   */
  public static function addStringToStringList($ExistingStringList, $StringToAdd)
  {
    $CommaString = "";
    if (strlen($StringToAdd) == 0) {
      $ReturnString = $ExistingStringList;
    } else {
      if (strlen($ExistingStringList) != 0) {
        $CommaString = ", ";
      }
      $ReturnString = $ExistingStringList . $CommaString . $StringToAdd;
    }

    return ($ReturnString);
  }
  /**
   * [parseNameValueStringIntoArray Parse Name Value String Into Array]
   * @method parseNameValueStringIntoArray
   * @param  [String]                        $NameValueString
   * @param  [Boolean]                       $boURLDecodeValues
   * @return [Array]                        [Parsed Array]
   */
  public static function parseNameValueStringIntoArray($NameValueString, $boURLDecodeValues)
  {
    // break the reponse into an array
    // first break the variables up using the "&" delimter
    $aPostVariables = explode("&", $NameValueString);
    $aParsedVariables = array();
    foreach ($aPostVariables as $Variable) {
      // for each variable, split is again on the "=" delimiter
      // to give name/value pairs
      $aSingleVariable = explode("=", $Variable);
      $Name = $aSingleVariable[0];
      if (!$boURLDecodeValues) {
        $Value = $aSingleVariable[1];
      }
      else {
        $Value = urldecode($aSingleVariable[1]);
      }
      $aParsedVariables[$Name] = $Value;
    }
    return ($aParsedVariables);
  }

  /**
   * [getMonthList MOnth list for dropdowns ]
   * @method getMonthList
   * @param  [String]       $DateMonth [allowed null - if added allows selected month to be added]
   * @return [String]       [String of HTML value for addition to HTML doc]
   */
  public static function getMonthList($DateMonth = null)
  {
    $lilDateMonthList = new ListItemList();

    for ($LoopIndex = 1; $LoopIndex <= 12; $LoopIndex++) {
      $DisplayMonth = $LoopIndex;
      if ($LoopIndex < 10) {
        $DisplayMonth = "0".$LoopIndex;
      }
      if ($DateMonth != "" &&
      $DateMonth == $LoopIndex) {
        $lilDateMonthList->add($DisplayMonth, $DisplayMonth, true);
      }
      else {
        $lilDateMonthList->add($DisplayMonth, $DisplayMonth, false);
      }
    }
    $lilDateMonthList=$lilDateMonthList->toString();
    return ($lilDateMonthList);
  }
  /**
   * [getStartYearList Start Year list for dropdowns ]
   * @method getStartYearList
   * @param  [String]       $StartDateYear [allowed null - if added allows selected month to be added]
   * @return [String]       [String of HTML value for addition to HTML doc]
   */
  public static function getStartYearList($StartDateYear = null)
  {
    $ThisYear = date("Y");
    $ThisYearMinusTen = $ThisYear - 10;

    $lilStartDateYearList = new ListItemList();

    for ($LoopIndex = $ThisYear; $LoopIndex >= $ThisYearMinusTen; $LoopIndex--) {
      $ShortYear=substr($LoopIndex, strlen($LoopIndex)-2, 2);
      if ($StartDateYear != "" &&
      $StartDateYear == $ShortYear) {
        $lilStartDateYearList->add($LoopIndex, $ShortYear, true);
      }
      else {
        $lilStartDateYearList->add($LoopIndex, $ShortYear, false);
      }
    }
    $lilStartDateYearList=$lilStartDateYearList->toString();
    return ($lilStartDateYearList);
  }
  /**
   * [getExpiryYearList Start Year list for dropdowns ]
   * @method getExpiryYearList
   * @param  [String]       $ExpiryDateYear [allowed null - if added allows selected Year to be added]
   * @return [String]       [String of HTML value for addition to HTML doc]
   */
  public static function getExpiryYearList($ExpiryDateYear = null)
  {
    $ThisYear = date("Y");
    $ThisYearPlusTen = $ThisYear + 10;

    $lilExpiryDateYearList = new ListItemList();

    for ($LoopIndex = $ThisYear; $LoopIndex <= $ThisYearPlusTen; $LoopIndex++) {
      $ShortYear=substr($LoopIndex, strlen($LoopIndex)-2, 2);
      if ($ExpiryDateYear != "" &&
      $ExpiryDateYear == $ShortYear) {
        $lilExpiryDateYearList->add($LoopIndex, $ShortYear, true);
      }
      else {
        $lilExpiryDateYearList->add($LoopIndex, $ShortYear, false);
      }
    }
    $lilExpiryDateYearList=$lilExpiryDateYearList->toString();
    return ($lilExpiryDateYearList);
  }
  /* ############################################## */
  /* ISO Functions */
  /* ############################################## */
  /**
   * [getISOCode Get ISO code from Short code]
   * @method getISOCode
   * @param  [String]     $isocode [ISO Short alpha code]
   * @return [Int]     [ISO Short numeric code]
   */
  public static function getISOCode($isocode)
  {
    $isocurrency = new ISO\ISOCurrencies;
    $return = $isocurrency->getISOCurrencyList()->getISOCurrencyCode($isocode);
    return $return;
  }
  /**
   * [getCountryCode description]
   * @method getCountryCode
   * @param  [String]     $countryshort [ISO Short alpha code]
   * @return [Int]     [ISO Short numeric code]
   */
  public static function getCountryCode($countryshort)
  {
    $isocountries = new ISO\ISOCountries;
    $return = $isocountries->getISOCountryList()->getISOCountry('Short', $countryshort);
    return $return;
  }
  /**
   * [getMinorCurrencyAmount Get exponent to conver major currency to minor (i.e. £1 to 100p)]
   * @method getMinorCurrencyAmount
   * @param  [Int]                 $curr [Currency code]
   * @param  [Float]                 $amt  [Major currency amount ]
   * @return [Int]                 [Minor currency amount]
   */
  public static function getMinorCurrencyAmount($curr, $amt)
  {
    $isocurrency = new ISO\ISOCurrencies;
    $exponent =   $isocurrency->getISOCurrencyList()->getISOExponent($curr);
    $currmap = pow(10, $exponent);
    $return = $amt * $currmap;
    return $return;
  }
  /**
   * [getCurrencySymbol return currency symbol for supported currencies]
   * @method getCurrencySymbol
   * @param  [String]            $curr [ISO Currency code]
   * @return [String]            [Currency symbol (HTML code)]
   */
  public static function getCurrencySymbol($curr)
  {
    $isocurrency = new ISO\ISOCurrencies;
    $isocode = $isocurrency->getISOCurrencyList()->getISOCurrencyCode($curr);
    $symbol =   $isocurrency->getISOCurrencyList()->getCurrencySymbol($isocode);
    return $symbol;
  }
  /**
   * [getFormattededCurrencyAmount Format and convert currency to correct expondent and symbol]
   * @method getFormattededCurrencyAmount
   * @param  [Int]                       $curr [ISO Currency Code]
   * @param  [Int]                       $amt  [Minor currency amount]
   * @return [String]                       [Formatted currency amount]
   */
  public static function getFormattededCurrencyAmount($curr, $amt)
  {
    $isocurrency = new ISO\ISOCurrencies;
    $exponent =   $isocurrency->getISOCurrencyList()->getISOExponent($curr);
    $symbol =   $isocurrency->getISOCurrencyList()->getCurrencySymbol($curr);
    $currshort =   $isocurrency->getISOCurrencyList()->getISOCurrencyShort($curr);
    $currmap = pow(10, $exponent);
    $return = $amt / $currmap;
    $return = $symbol . " " . number_format($return,2,'.',',') . " | " . $currshort;
    return $return;
  }
  /**
   * [getMajorAmount Convert minor amount to major amount]
   * @method getMajorAmount
   * @param  [Int]         $curr [ISO Currency Code]
   * @param  [Int]         $amt  [Minor currency amount ]
   * @return [Int]         [Major amount]
   */
  public static function getMajorAmount($curr, $amt)
  {
    $isocurrency = new ISO\ISOCurrencies;
    $exponent =   $isocurrency->getISOCurrencyList()->getISOExponent($curr);
    $currmap = pow(10, $exponent);
    $return = $amt / $currmap;
    return $return;
  }
  /**
   * [getCountryDropDownlist Create string for country select dropdown list]
   * @method getCountryDropDownlist
   * @param  [Int]                 $ISOCountry [ISO code for selected country, allowed null]
   * @return [String]                 [HTML code as string for select options]
   */
  public static function getCountryDropDownlist($ISOCountry = null)
  {
    $isocountries = new ISO\ISOCountries; //open countries class list
    $iclISOCountryList = $isocountries->getISOCountryList();
    $lilISOCountryList = new ListItemList(); //open list item creation class

    //loop through all countries to add to the select list and check which is selected (if detail has been set)
    $lilISOCountryList->add("--------------------", "", false);// add a dummy line
    for ($LoopIndex = 0; $LoopIndex < $iclISOCountryList->getCount()-1; $LoopIndex++) {
      if ($ISOCountry) {
        $selected = ($iclISOCountryList->getISOCountry('Name', $ISOCountry) == $iclISOCountryList->getAt($LoopIndex)->getISOCode() || $iclISOCountryList->getISOCountry('Short', $ISOCountry) == $iclISOCountryList->getAt($LoopIndex)->getISOCode()) ? true : false;
      }// check if ISOcountry has been provided, and if has check if the current country is selected - options are search by Name, ISO or Short
      $lilISOCountryList->add($iclISOCountryList->getAt($LoopIndex)->getCountryName(), $iclISOCountryList->getAt($LoopIndex)->getCountryShort(), $selected);
    }
    $return = $lilISOCountryList->toString();
    return $return;
  }


  /* ############################################## */
  /* Hash & String Functions */
  /* ############################################## */
  /**
   * [calculateHashDigest Hash calculation function]
   * @method calculateHashDigest
   * @param  [String]              $input      [input string to hash]
   * @param  [String]              $key        [secret or preshared key]
   * @param  [String]              $hashmethod [Hash method to select and hash the string]
   * @return [String]              [calculated hash digest]
   */
  public static function calculateHashDigest($input, $key, $hashmethod)
  {
    switch ($hashmethod) {
      case "MD5":
      $response = md5($input);
      break;
      case "SHA1":
      $response = sha1($input);
      break;
      case "HMACMD5":
      $response = hash_hmac("md5", $input, $key);
      break;
      case "HMACSHA1":
      $response = hash_hmac("sha1", $input, $key);
      break;
      default:
      $response = md5($input);
      break;
    }

    return ($response);
  }
  /**
   * [generateStringToHashDirect Create string from variables to pass to hash functions]
   * @method generateStringToHashDirect
   * @param  [Int]                     $Amount
   * @param  [Int]                     $CurrencyShort
   * @param  [String]                     $OrderID
   * @param  [String]                     $OrderDescription
   * @param  [String]                     $SecretKey
   * @return [String]                     [String to be hashed]
   */
  public static function generateStringToHashDirect($Amount, $CurrencyShort, $OrderID, $OrderDescription, $SecretKey)
  {
    $ReturnString = "Amount=".$Amount. "&CurrencyShort=".$CurrencyShort. "&OrderID=".$OrderID. "&OrderDescription=".$OrderDescription. "&SecretKey=".$SecretKey;

    return ($ReturnString);
  }
  /**
   * [generateStringToHashTransparentInitial Create string from variables to pass to hash functions]
   * @method generateStringToHashTransparentInitial
   * @param  [String]                                 $HashMethod
   * @param  [String]                                 $PreSharedKey
   * @param  [String]                                 $MerchantID
   * @param  [String]                                 $Password
   * @param  [Float]                                  $Amount
   * @param  [Int]                                    $CurrencyCode
   * @param  [String]                                 $OrderID
   * @param  [String]                                 $TransactionType
   * @param  [String]                                 $TransactionDateTime
   * @param  [String]                                 $CallbackURL
   * @param  [String]                                 $OrderDescription
   * @return [String]                                 [String to be hashed]
   */
  public static function generateStringToHashTransparentInitial($HashMethod, $PreSharedKey, $MerchantID, $Password, $Amount, $CurrencyCode, $OrderID, $TransactionType, $TransactionDateTime, $CallbackURL, $OrderDescription)
  {
    switch ($HashMethod) {
      case "MD5":
      case "SHA1":
      $boIncludePreSharedKeyInString = true;
      break;
    }
    $ReturnString = "";
    if ($boIncludePreSharedKeyInString) {
      $ReturnString = "PreSharedKey=" . $PreSharedKey . "&";
    }
    $ReturnString .= "MerchantID=" . $MerchantID . "&Password=" . $Password . "&Amount=" . $Amount . "&CurrencyCode=" . $CurrencyCode . "&OrderID=" . $OrderID . "&TransactionType=" . $TransactionType . "&TransactionDateTime=" . $TransactionDateTime . "&CallbackURL=" . $CallbackURL . "&OrderDescription=" . $OrderDescription;
    return $ReturnString;
  }

  /**
   * [generateStringToHashHosted Create string from variables to pass to hash functions]
   * @method generateStringToHashHosted
   * @param  [String]                     $MerchantID
   * @param  [String]                     $Password
   * @param  [Float]                      $Amount
   * @param  [Int]                        $CurrencyCode
   * @param  [String]                     $EchoAVSCheckResult
   * @param  [String]                     $EchoCV2CheckResult
   * @param  [String]                     $EchoThreeDSecureAuthenticationCheckResult
   * @param  [String]                     $EchoCardType
   * @param  [String]                     $OrderID
   * @param  [String]                     $TransactionType
   * @param  [String]                     $TransactionDateTime
   * @param  [String]                     $CallbackURL
   * @param  [String]                     $OrderDescription
   * @param  [String]                     $CustomerName
   * @param  [String]                     $Address1
   * @param  [String]                     $Address2
   * @param  [String]                     $Address3
   * @param  [String]                     $Address4
   * @param  [String]                     $City
   * @param  [String]                     $State
   * @param  [String]                     $PostCode
   * @param  [Int]                        $CountryCode
   * @param  [String]                     $CV2Mandatory
   * @param  [String]                     $Address1Mandatory
   * @param  [String]                     $CityMandatory
   * @param  [String]                     $PostCodeMandatory
   * @param  [String]                     $StateMandatory
   * @param  [String]                     $CountryMandatory
   * @param  [String]                     $ResultDeliveryMethod
   * @param  [String]                     $ServerResultURL
   * @param  [String]                     $PaymentFormDisplaysResult
   * @param  [String]                     $PreSharedKey
   * @param  [String]                     $HashMethod
   * @return [String]                     [String to be hashed]
   */
  public static function generateStringToHashHosted($MerchantID,$Password,$Amount,$CurrencyCode,$EchoAVSCheckResult,$EchoCV2CheckResult,$EchoThreeDSecureAuthenticationCheckResult,$EchoCardType,$OrderID,$TransactionType,$TransactionDateTime,$CallbackURL,$OrderDescription,$CustomerName,$Address1,$Address2,$Address3,$Address4,$City,$State,$PostCode,$CountryCode,$CV2Mandatory,$Address1Mandatory,$CityMandatory,$PostCodeMandatory,$StateMandatory,$CountryMandatory,$ResultDeliveryMethod,$ServerResultURL,$PaymentFormDisplaysResult,$PreSharedKey,$HashMethod) {
    $ReturnString = "";
    switch ($HashMethod) {
      case "MD5":
        $boIncludePreSharedKeyInString = true;
        break;
      case "SHA1":
        $boIncludePreSharedKeyInString = true;
        break;
      case "HMACMD5":
        $boIncludePreSharedKeyInString = false;
        break;
      case "HMACSHA1":
        $boIncludePreSharedKeyInString = false;
        break;
    }
    if ($boIncludePreSharedKeyInString) {
      $ReturnString = "PreSharedKey=" . $PreSharedKey . "&";
    }
    $ReturnString .="MerchantID=" . $MerchantID ."&Password=" . $Password ."&Amount=" . $Amount."&CurrencyCode=" . $CurrencyCode ."&EchoAVSCheckResult=" . $EchoAVSCheckResult ."&EchoCV2CheckResult=" . $EchoCV2CheckResult ."&EchoThreeDSecureAuthenticationCheckResult=" . $EchoThreeDSecureAuthenticationCheckResult ."&EchoCardType=" . $EchoCardType."&OrderID=" . $OrderID ."&TransactionType=" . $TransactionType . "&TransactionDateTime=" . $TransactionDateTime ."&CallbackURL=" . $CallbackURL ."&OrderDescription=" . $OrderDescription ."&CustomerName=" . $CustomerName ."&Address1=" . $Address1 ."&Address2=" . $Address2 ."&Address3=" . $Address3 ."&Address4=" . $Address4 ."&City=" . $City ."&State=" . $State ."&PostCode=" . $PostCode ."&CountryCode=" . $CountryCode ."&CV2Mandatory=" . $CV2Mandatory ."&Address1Mandatory=" . $Address1Mandatory ."&CityMandatory=" . $CityMandatory ."&PostCodeMandatory=" . $PostCodeMandatory ."&StateMandatory=" . $StateMandatory ."&CountryMandatory=" . $CountryMandatory ."&ResultDeliveryMethod=" . $ResultDeliveryMethod ."&ServerResultURL=" . $ServerResultURL ."&PaymentFormDisplaysResult=" . $PaymentFormDisplaysResult .'&ServerResultURLCookieVariables=' . '&ServerResultURLFormVariables=' . '&ServerResultURLQueryStringVariables=';

    return $ReturnString;
  }
  /**
   * [checkIntegrityOfIncomingVariablesDirect verify variables being passed back to the payment processor]
   * @method checkIntegrityOfIncomingVariablesDirect
   * @param  [String]                                  $ivsIncomingVariableSource
   * @param  [Array]                                   $aVariables
   * @param  [String]                                  $Hash
   * @param  [String]                                  $SecretKey
   * @return [Boolean]                                 [False if hash validation fails]
   */
  public static function checkIntegrityOfIncomingVariablesDirect($ivsIncomingVariableSource, $aVariables, $Hash, $SecretKey)
  {
    $boReturnValue = false;
    $StringToHash = null;
    $CalculatedHash = null;

    switch ($ivsIncomingVariableSource) {
      case "SHOPPING_CART_CHECKOUT":
        $StringToHash = self::generateStringToHashDirect($aVariables["Amount"],
        $aVariables["CurrencyCode"],
        $aVariables["OrderID"],
        $aVariables["OrderDescription"],
        $SecretKey);
        break;
      case "RESULTS":
        $StringToHash = self::generateStringToHashDirect($aVariables["Amount"],
        $aVariables["CurrencyCode"],
        $aVariables["OrderID"],
        $aVariables["OrderDescription"],
        $SecretKey);
        break;
      case "THREE_D_SECURE":
        $StringToHash = self::generateStringToHash2($aVariables["PaRES"],
        $aVariables["CrossReference"],
        $SecretKey);
        break;
    }
    $CalculatedHash = self::calculateHashDigest($StringToHash, $SecretKey, $aVariables["HashMethod"]);
    if ($CalculatedHash == $Hash) {
      $boReturnValue = true;
    }
    return ($boReturnValue);
  }
  /**
   * [generateStringToHashQueryString Create string from variables to pass to hash functions]
   * @method generateStringToHashQueryString
   * @param  [String]                          $MerchantID
   * @param  [String]                          $Password
   * @param  [String]                          $CrossReference
   * @param  [String]                          $OrderID
   * @param  [String]                          $PreSharedKey
   * @param  [String]                          $HashMethod
   * @return [String]                          [prepared string to be passed to hash functions ]
   */
  public static function generateStringToHashQueryString($MerchantID, $Password, $CrossReference, $OrderID, $PreSharedKey, $HashMethod)
  {
    $ReturnString = "";
    switch ($HashMethod) {
      case "MD5":
        $boIncludePreSharedKeyInString = true;
        break;
      case "SHA1":
        $boIncludePreSharedKeyInString = true;
        break;
      case "HMACMD5":
        $boIncludePreSharedKeyInString = false;
        break;
      case "HMACSHA1":
        $boIncludePreSharedKeyInString = false;
        break;
    }
    if ($boIncludePreSharedKeyInString) {
      $ReturnString = "PreSharedKey=" . $PreSharedKey . "&";
    }
    $ReturnString = $ReturnString . "MerchantID=" . $MerchantID . "&Password=" . $Password . "&CrossReference=" . $CrossReference . "&OrderID=" . $OrderID;

    return ($ReturnString);
  }
  /**
   * [generateStringToTransactionResult Pull information from transaction result object and colalte into a string to be hash]
   * @method generateStringToTransactionResult
   * @param  [String]                            $MerchantID
   * @param  [String]                            $Password
   * @param  [Object] TransactionResult          $trTransactionResult
   * @param  [String]                            $PreSharedKey
   * @param  [String]                            $HashMethod
   * @return [String]                            [Prepared string to be passed to hash functions]
   */
  public static function generateStringToTransactionResult(
    $MerchantID,
    $Password,
    TransactionResult $trTransactionResult,
    $PreSharedKey,
    $HashMethod
  ) {
    $ReturnString = "";
    switch ($HashMethod) {
      case "MD5":
        $boIncludePreSharedKeyInString = true;
        break;
      case "SHA1":
        $boIncludePreSharedKeyInString = true;
        break;
      case "HMACMD5":
        $boIncludePreSharedKeyInString = false;
        break;
      case "HMACSHA1":
        $boIncludePreSharedKeyInString = false;
        break;
    }
    if ($boIncludePreSharedKeyInString) {
      $ReturnString = "PreSharedKey=" . $PreSharedKey . "&";
    }
    $ReturnString =$ReturnString . "MerchantID=" . $MerchantID ."&Password=" . $Password . "&StatusCode=" . $trTransactionResult->getStatusCode() . "&Message=" . $trTransactionResult->getMessage() . "&PreviousStatusCode=" . $trTransactionResult->getPreviousStatusCode() . "&PreviousMessage=" . $trTransactionResult->getPreviousMessage() . "&CrossReference=" . $trTransactionResult->getCrossReference();
    // include the option variables if they are present
    $addressNumericCheckResult = $trTransactionResult->getAddressNumericCheckResult();
    if (isset($addressNumericCheckResult)) {
      $ReturnString .= "&AddressNumericCheckResult=" . $addressNumericCheckResult . "&PostCodeCheckResult=" . $trTransactionResult->getPostCodeCheckResult();
    }
    $CV2CheckResult = $trTransactionResult->getCV2CheckResult();
    if (isset($CV2CheckResult)) {
      $ReturnString .= "&CV2CheckResult=" . $CV2CheckResult;
    }
    $threeDSecureAuthenticationCheckResult = $trTransactionResult->getThreeDSecureAuthenticationCheckResult();
    if (isset($threeDSecureAuthenticationCheckResult)) {
      $ReturnString .= "&ThreeDSecureAuthenticationCheckResult=" . $threeDSecureAuthenticationCheckResult;
    }
    $cardType = $trTransactionResult->getCardType();
    if (isset($cardType)) {
      $ReturnString .= "&CardType=" . $cardType . "&CardClass=" . $trTransactionResult->getCardClass() . "&CardIssuer=" . $trTransactionResult->getCardIssuer() . "&CardIssuerCountryCode=" . $trTransactionResult->getCardIssuerCountryCode();
    }
    $ReturnString = $ReturnString . "&Amount=" . $trTransactionResult->getAmount() . "&CurrencyCode=" . $trTransactionResult->getCurrencyCode() . "&OrderID=" . $trTransactionResult->getOrderID() . "&TransactionType=" . $trTransactionResult->getTransactionType() . "&TransactionDateTime=" . $trTransactionResult->getTransactionDateTime() . "&OrderDescription=" . $trTransactionResult->getOrderDescription() . "&CustomerName=" . $trTransactionResult->getCustomerName() . "&Address1=" . $trTransactionResult->getAddress1() . "&Address2=" . $trTransactionResult->getAddress2() . "&Address3=" . $trTransactionResult->getAddress3() . "&Address4=" . $trTransactionResult->getAddress4() . "&City=" . $trTransactionResult->getCity() . "&State=" . $trTransactionResult->getState() . "&PostCode=" . $trTransactionResult->getPostCode() . "&CountryCode=" . $trTransactionResult->getCountryCode();
    $emailAddress = $trTransactionResult->getEmailAddress();
    if (isset($emailAddress)) {
      $ReturnString .= "&EmailAddress=" . $emailAddress;
    }
    $phoneNumber = $trTransactionResult->getPhoneNumber();
    if (isset($phoneNumber)) {
      $ReturnString .= "&PhoneNumber=" . $phoneNumber;
    }

    return ($ReturnString);
  }
  /**
   * [generateStringToHashPaymentComplete Transaprent redirect generate string to hash to verify payment completed]
   * @method generateStringToHashPaymentComplete
   * @param  [Object] TransactionResult             $trTransactionResult
   * @param  [String]                              $HashMethod
   * @param  [String]                              $PreSharedKey
   * @param  [String]                              $MerchantID
   * @param  [String]                              $Password
   * @return [String]                              [Prepeared string to be passed to hash functions]
   */
  public static function generateStringToHashPaymentComplete($trTransactionResult, $HashMethod, $PreSharedKey, $MerchantID, $Password)
  {
    $ReturnString = null;
    switch ($HashMethod) {
      case "MD5":
      case "SHA1":
        $boIncludePreSharedKeyInString = true;
        break;
    }
    if ($boIncludePreSharedKeyInString) {
      $ReturnString = "PreSharedKey=" . $PreSharedKey . "&";
    }
    $ReturnString .= "MerchantID=" . $MerchantID . "&Password=" . $Password . "&StatusCode=" . $trTransactionResult->getStatusCode() . "&Message=" . $trTransactionResult->getMessage() . "&PreviousStatusCode=" . $trTransactionResult->getPreviousStatusCode() . "&PreviousMessage=" . $trTransactionResult->getPreviousMessage() . "&CrossReference=" . $trTransactionResult->getCrossReference() . "&AddressNumericCheckResult=" . $trTransactionResult->getAddressNumericCheckResult() . "&PostCodeCheckResult=" . $trTransactionResult->getPostCodeCheckResult() . "&CV2CheckResult=" . $trTransactionResult->getCV2CheckResult() . "&ThreeDSecureAuthenticationCheckResult=" . $trTransactionResult->getThreeDSecureAuthenticationCheckResult() . "&CardType=" . $trTransactionResult->getCardType() . "&CardClass=" . $trTransactionResult->getCardClass() . "&CardIssuer=" . $trTransactionResult->getCardIssuer() . "&CardIssuerCountryCode=" . $trTransactionResult->getCardIssuerCountryCode() . "&Amount=" . $trTransactionResult->getAmount() . "&CurrencyCode=" . $trTransactionResult->getCurrencyCode() . "&OrderID=" . $trTransactionResult->getOrderID() . "&TransactionType=" . $trTransactionResult->getTransactionType() . "&TransactionDateTime=" . $trTransactionResult->getTransactionDateTime() . "&OrderDescription=" . $trTransactionResult->getOrderDescription() . "&Address1=" . $trTransactionResult->getAddress1() . "&Address2=" . $trTransactionResult->getAddress2() . "&Address3=" . $trTransactionResult->getAddress3() . "&Address4=" . $trTransactionResult->getAddress4() . "&City=" . $trTransactionResult->getCity() . "&State=" . $trTransactionResult->getState() . "&PostCode=" . $trTransactionResult->getPostCode() . "&CountryCode=" . $trTransactionResult->getCountryCode() . "&EmailAddress=" . $trTransactionResult->getEmailAddress() . "&PhoneNumber=" . $trTransactionResult->getPhoneNumber();
    return ($ReturnString);
  }
  /**
   * [generateStringToHash3DSecurePostAuthentication transparent redirect generate string to hash to verify 3d secure post authentication]
   * @method generateStringToHash3DSecurePostAuthentication
   * @param  [String]                                         $HashMethod
   * @param  [String]                                         $PreSharedKey
   * @param  [String]                                         $MerchantID
   * @param  [String]                                         $Password
   * @param  [String]                                         $CrossReference
   * @param  [String]                                         $TransactionDateTime
   * @param  [String]                                         $CallbackURL
   * @param  [String]                                         $PaRES
   * @return [String]                                         [Prepared string to be passed back to the hash function]
   */
  public static function generateStringToHash3DSecurePostAuthentication($HashMethod, $PreSharedKey, $MerchantID, $Password, $CrossReference, $TransactionDateTime, $CallbackURL, $PaRES)
  {
    $ReturnString = "";
    switch ($HashMethod) {
      case "MD5":
      case "SHA1":
      $boIncludePreSharedKeyInString = true;
      break;
    }
    if ($boIncludePreSharedKeyInString) {
      $ReturnString = "PreSharedKey=" . $PreSharedKey . "&";
    }
    $ReturnString .= "MerchantID=" . $MerchantID . "&Password=" . $Password . "&CrossReference=" . $CrossReference . "&TransactionDateTime=" . $TransactionDateTime . "&CallbackURL=" . $CallbackURL . "&PaRES=" . $PaRES;
    return $ReturnString;
  }


  /* ############################################## */
  /* Response Validation functions */
  /* ############################################## */
  /**
   * [getTransactionReferenceFromQueryString Retrieve transaction reference from query strings]
   * @method getTransactionReferenceFromQueryString
   * @param  [Array]                                 $aQueryStringVariables
   * @param  [String]                                 $CrossReference
   * @param  [String]                                 $OrderID
   * @param  [String]                                 $HashDigest
   * @param  [String]                                 $OutputMessage
   * @return [Boolean]                                 [False on error]
   */
  public static function getTransactionReferenceFromQueryString($aQueryStringVariables, &$CrossReference, &$OrderID, &$HashDigest, &$OutputMessage)
  {
    $HashDigest = "";
    $OutputMessage = "";
    $boErrorOccurred = false;
    try {
      // hash digest
      if (isset($aQueryStringVariables["HashDigest"])) {
        $HashDigest = $aQueryStringVariables["HashDigest"];
      }
      // cross reference of transaction
      if (!isset($aQueryStringVariables["CrossReference"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [CrossReference] not received");
        $boErrorOccurred = true;
      } else {
        $CrossReference = $aQueryStringVariables["CrossReference"];
      }
      // order ID (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aQueryStringVariables["OrderID"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [OrderID] not received");
        $boErrorOccurred = true;
      } else {
        $OrderID = $aQueryStringVariables["OrderID"];
      }
    } catch (Exception $e) {
      $boErrorOccurred = true;
      $OutputMessage = $e->getMessage();
    }

    return (!$boErrorOccurred);
  }
  /**
   * [getTransactionResultFromPostVariables Get transaction result object from post variables]
   * @method getTransactionResultFromPostVariables
   * @param  [Array]                                 $aFormVariables
   * @param  [Object] TranactionResult               $trTransactionResult
   * @param  [String]                                $HashDigest
   * @param  [String]                                $OutputMessage
   * @param  [String]                                $integration_type
   * @return [Boolean]                               [False on error]
   */
  public static function getTransactionResultFromPostVariables($aFormVariables, &$trTransactionResult, &$HashDigest, &$OutputMessage, $integration_type)
  {
    $trTransactionResult = null;
    $HashDigest = "";
    $OutputMessage = "";
    $boErrorOccurred = false;
    try {
      // hash digest
      if (isset($aFormVariables["HashDigest"])) {
        $HashDigest = $aFormVariables["HashDigest"];
      }
      // transaction status code
      if (!isset($aFormVariables["StatusCode"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [StatusCode] not received");
        $boErrorOccurred = true;
      }
      else {
        if ($aFormVariables["StatusCode"] == "") {
          $nStatusCode = null;
        }
        else {
          $nStatusCode = intval($aFormVariables["StatusCode"]);
        }
      }
      // transaction message
      if (!isset($aFormVariables["Message"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [Message] not received");
        $boErrorOccurred = true;
      }
      else {
        $Message = $aFormVariables["Message"];
      }
      // status code of original transaction if this transaction was deemed a duplicate
      if (!isset($aFormVariables["PreviousStatusCode"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [PreviousStatusCode] not received");
        $boErrorOccurred = true;
      }
      else {
        if ($aFormVariables["PreviousStatusCode"] == "") {
          $nPreviousStatusCode = null;
        }
        else {
          $nPreviousStatusCode = intval($aFormVariables["PreviousStatusCode"]);
        }
      }
      // status code of original transaction if this transaction was deemed a duplicate
      if (!isset($aFormVariables["PreviousMessage"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [PreviousMessage] not received");
        $boErrorOccurred = true;
      }
      else {
        $PreviousMessage = $aFormVariables["PreviousMessage"];
      }
      // cross reference of transaction
      if (!isset($aFormVariables["CrossReference"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [CrossReference] not received");
        $boErrorOccurred = true;
      }
      else {
        $CrossReference = $aFormVariables["CrossReference"];
      }
      // result of the address numeric check (only returned if EchoAVSCheckResult is set as true on the input)
      if (isset($aFormVariables["AddressNumericCheckResult"])) {
        $AddressNumericCheckResult = $aFormVariables["AddressNumericCheckResult"];
      }
      // result of the post code check (only returned if EchoAVSCheckResult is set as true on the input)
      if (isset($aFormVariables["PostCodeCheckResult"])) {
        $PostCodeCheckResult = $aFormVariables["PostCodeCheckResult"];
      }
      // result of the CV2 check (only returned if EchoCV2CheckResult is set as true on the input)
      if (isset($aFormVariables["CV2CheckResult"])) {
        $CV2CheckResult = $aFormVariables["CV2CheckResult"];
      }
      // result of the 3DSecure check (only returned if EchoThreeDSecureAuthenticationCheckResult is set as true on the input)
      if (isset($aFormVariables["ThreeDSecureAuthenticationCheckResult"])) {
        $ThreeDSecureAuthenticationCheckResult = $aFormVariables["ThreeDSecureAuthenticationCheckResult"];
      }
      // card type (only returned if EchoCardType is set as true on the input)
      if (isset($aFormVariables["CardType"])) {
        $CardType = $aFormVariables["CardType"];
      }
      // card class (only returned if EchoCardType is set as true on the input)
      if (isset($aFormVariables["CardClass"])) {
        $CardClass = $aFormVariables["CardClass"];
      }
      // card issuer (only returned if EchoCardType is set as true on the input)
      if (isset($aFormVariables["CardIssuer"])) {
        $CardIssuer = $aFormVariables["CardIssuer"];
      }
      // card issuer country code (only returned if EchoCardType is set as true on the input)
      if (isset($aFormVariables["CardIssuerCountryCode"])) {
        $nCardIssuerCountryCode = $aFormVariables["CardIssuerCountryCode"];
      }
      // amount (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aFormVariables["Amount"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [Amount] not received");
        $boErrorOccurred = true;
      }
      else {
        if ($aFormVariables["Amount"] == null) {
          $nAmount = null;
        }
        else {
          $nAmount = intval($aFormVariables["Amount"]);
        }
      }
      // currency code (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aFormVariables["CurrencyCode"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [CurrencyCode] not received");
        $boErrorOccurred = true;
      }
      else {
        if ($aFormVariables["CurrencyCode"] == null) {
          $nCurrencyCode = null;
        }
        else {
          $nCurrencyCode = intval($aFormVariables["CurrencyCode"]);
        }
      }
      // order ID (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aFormVariables["OrderID"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [OrderID] not received");
        $boErrorOccurred = true;
      }
      else {
        $OrderID = $aFormVariables["OrderID"];
      }
      // transaction type (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aFormVariables["TransactionType"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [TransactionType] not received");
        $boErrorOccurred = true;
      }
      else {
        $TransactionType = $aFormVariables["TransactionType"];
      }
      // transaction date/time (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aFormVariables["TransactionDateTime"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [TransactionDateTime] not received");
        $boErrorOccurred = true;
      }
      else {
        $TransactionDateTime = $aFormVariables["TransactionDateTime"];
        $TransactionDateTime = str_replace(' 00:00', '+00:00', $TransactionDateTime);//Tactical fix to allow date time strings to be converted back to real date times
      }
      // order description (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aFormVariables["OrderDescription"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [OrderDescription] not received");
        $boErrorOccurred = true;
      }
      else {
        $OrderDescription = $aFormVariables["OrderDescription"];
      }
      // customer name (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aFormVariables["CustomerName"]) && $integration_type == 'hosted') {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [CustomerName] not received");
        $boErrorOccurred = true;
      }
      elseif (isset($aFormVariables["CustomerName"])) {
        $CustomerName = $aFormVariables["CustomerName"];
      }
      // address1 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aFormVariables["Address1"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [Address1] not received");
        $boErrorOccurred = true;
      }
      else {
        $Address1 = $aFormVariables["Address1"];
      }
      // address2 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aFormVariables["Address2"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [Address2] not received");
        $boErrorOccurred = true;
      }
      else {
        $Address2 = $aFormVariables["Address2"];
      }
      // address3 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aFormVariables["Address3"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [Address3] not received");
        $boErrorOccurred = true;
      }
      else {
        $Address3 = $aFormVariables["Address3"];
      }
      // address4 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aFormVariables["Address4"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [Address4] not received");
        $boErrorOccurred = true;
      }
      else {
        $Address4 = $aFormVariables["Address4"];
      }
      // city (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aFormVariables["City"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [City] not received");
        $boErrorOccurred = true;
      }
      else {
        $City = $aFormVariables["City"];
      }
      // state (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aFormVariables["State"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [State] not received");
        $boErrorOccurred = true;
      }
      else {
        $State = $aFormVariables["State"];
      }
      // post code (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aFormVariables["PostCode"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [PostCode] not received");
        $boErrorOccurred = true;
      }
      else {
        $PostCode = $aFormVariables["PostCode"];
      }
      // country code (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aFormVariables["CountryCode"])) {
        $OutputMessage = PayzoneHelper::addStringToStringList($OutputMessage, "Expected variable [CountryCode] not received");
        $boErrorOccurred = true;
      }
      else {
        if ($aFormVariables["CountryCode"] == "") {
          $nCountryCode = null;
        }
        else {
          $nCountryCode = intval($aFormVariables["CountryCode"]);
        }
      }
      // email address (only returned if in the input)
      if (isset($aFormVariables["EmailAddress"])) {
        $EmailAddress = $aFormVariables["EmailAddress"];
      }
      // phone number (only returned if in the input)
      if (isset($aFormVariables["PhoneNumber"])) {
        $PhoneNumber = $aFormVariables["PhoneNumber"];
      }
      if (!$boErrorOccurred) {
        $trTransactionResult = new TransactionResult();
        $trTransactionResult->setStatusCode($nStatusCode); // transaction status code
        $trTransactionResult->setMessage($Message); // transaction message
        $trTransactionResult->setPreviousStatusCode($nPreviousStatusCode); // status code of original transaction if duplicate transaction
        $trTransactionResult->setPreviousMessage($PreviousMessage); // status code of original transaction if duplicate transaction
        $trTransactionResult->setCrossReference($CrossReference); // cross reference of transaction
        if (isset($AddressNumericCheckResult)) {
          $trTransactionResult->setAddressNumericCheckResult($AddressNumericCheckResult); // address numeric check result
        }
        if (isset($PostCodeCheckResult)) {
          $trTransactionResult->setPostCodeCheckResult($PostCodeCheckResult); // post code check result
        }
        if (isset($CV2CheckResult)) {
          $trTransactionResult->setCV2CheckResult($CV2CheckResult); // CV2 check result
        }
        if (isset($ThreeDSecureAuthenticationCheckResult)) {
          $trTransactionResult->setThreeDSecureAuthenticationCheckResult($ThreeDSecureAuthenticationCheckResult); // 3DSecure check result
        }
        if (isset($CardType)) {
          $trTransactionResult->setCardType($CardType); // card type
        }
        if (isset($CardClass)) {
          $trTransactionResult->setCardClass($CardClass); // card class
        }
        if (isset($CardIssuer)) {
          $trTransactionResult->setCardIssuer($CardIssuer); // card issuer
        }
        if (isset($nCardIssuerCountryCode)) {
          $trTransactionResult->setCardIssuerCountryCode($nCardIssuerCountryCode); // card issuer country code
        }
        $trTransactionResult->setAmount($nAmount); // amount echoed back
        $trTransactionResult->setCurrencyCode($nCurrencyCode); // currency code echoed back
        $trTransactionResult->setOrderID($OrderID); // order ID echoed back
        $trTransactionResult->setTransactionType($TransactionType); // transaction type echoed back
        $trTransactionResult->setTransactionDateTime($TransactionDateTime); // transaction date/time echoed back
        $trTransactionResult->setOrderDescription($OrderDescription); // order description echoed back
        // the customer details that were actually
        // processed (might be different
        // from those passed to the payment form)
        if ($integration_type == 'hosted') {
          $trTransactionResult->setCustomerName($CustomerName);
        }
        $trTransactionResult->setAddress1($Address1);
        $trTransactionResult->setAddress2($Address2);
        $trTransactionResult->setAddress3($Address3);
        $trTransactionResult->setAddress4($Address4);
        $trTransactionResult->setCity($City);
        $trTransactionResult->setState($State);
        $trTransactionResult->setPostCode($PostCode);
        $trTransactionResult->setCountryCode($nCountryCode);
        if (isset($EmailAddress)) {
          $trTransactionResult->setEmailAddress($EmailAddress); // customer's email address
        }
        if (isset($PhoneNumber)) {
          $trTransactionResult->setPhoneNumber($PhoneNumber); // customer's phone number
        }
      }
    } catch (Exception $e) {
      $boErrorOccurred = true;
      $OutputMessage = $e->getMessage();
    }

    return (!$boErrorOccurred);
  }

  /**
   * [get3DSecureAuthenticationRequiredFromPostVariables Populate transaction result object from variables array]
   * @method get3DSecureAuthenticationRequiredFromPostVariables
   * @param  [Array]                                             $aVariables
   * @param  [Object]                                             $trTransactionResult
   * @return [Boolean]                                             [False on failed]
   */
  public static function get3DSecureAuthenticationRequiredFromPostVariables($aVariables, &$trTransactionResult)
  {
    $HashDigest = "";
    $ErrorMessage = "";
    $boErrorActive = false;

    try {
      $trTransactionResult = new TransactionResult();

      // hash digest
      if (isset($aVariables["HashDigest"])) {
        $HashDigest = $aVariables["HashDigest"];
      }

      // transaction status code
      if (!isset($aVariables["StatusCode"])) {
        $ErrorMessage = PayzoneHelper::addStringToStringList($ErrorMessage, "Expected variable [StatusCode] not received");
        $boErrorActive = true;
      }
      else {
        if ($aVariables["StatusCode"] == "") {
          $trTransactionResult->setStatusCode(null);
        }
        else {
          $trTransactionResult->setStatusCode(intval($aVariables["StatusCode"]));
        }
      }
      // transaction message
      if (!isset($aVariables["Message"])) {
        $ErrorMessage = PayzoneHelper::addStringToStringList($ErrorMessage, "Expected variable [Message] not received");
        $boErrorActive = true;
      }
      else {
        $trTransactionResult->setMessage($aVariables["Message"]);
      }

      // cross reference of transaction
      if (!isset($aVariables["CrossReference"])) {
        $ErrorMessage = PayzoneHelper::addStringToStringList($ErrorMessage, "Expected variable [CrossReference] not received");
        $boErrorActive = true;
      }
      else {
        $trTransactionResult->setCrossReference($aVariables["CrossReference"]);
      }

      // currency code (same as value passed into payment form - echoed back out by payment form)
      // order ID (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aVariables["OrderID"])) {
        $ErrorMessage = PayzoneHelper::addStringToStringList($ErrorMessage, "Expected variable [OrderID] not received");
        $boErrorActive = true;
      }
      else {
        $trTransactionResult->setOrderID($aVariables["OrderID"]);
      }

      // transaction date/time (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aVariables["TransactionDateTime"])) {
        $ErrorMessage = PayzoneHelper::addStringToStringList($ErrorMessage, "Expected variable [TransactionDateTime] not received");
        $boErrorActive = true;
      }
      else {
        $trTransactionResult->setTransactionDateTime($aVariables["TransactionDateTime"]);
      }
      // order description (same as value passed into payment form - echoed back out by payment form)
      if (!isset($aVariables["ACSURL"])) {
        $ErrorMessage = PayzoneHelper::addStringToStringList($ErrorMessage, "Expected variable [ACSURL] not received");
        $boErrorActive = true;
      }
      else {
        $trTransactionResult->setACSURL($aVariables["ACSURL"]);
      }
      // address1 (not necessarily the same as value passed into payment form - as the customer can change it on the form)
      if (!isset($aVariables["PaREQ"])) {
        $ErrorMessage = PayzoneHelper::addStringToStringList($ErrorMessage, "Expected variable [PaREQ] not received");
        $boErrorActive = true;
      }
      else {
        $trTransactionResult->setPaREQ($aVariables["PaREQ"]);
      }
    } catch (Exception $e) {
      $boErrorActive = true;
      $ErrorMessage = $e->getMessage();
    }

    return (!$boErrorActive);
  }

  public static function validateTransactionResultHostedPost($MerchantID, $Password, $PreSharedKey, $HashMethod, $aPostVariables, &$trTransactionResult, &$ValidateErrorMessage)
  {
    $ValidateErrorMessage = "";
    $trTransactionResult = null;
    // read the transaction result variables from the post variable list
    if (!PayzoneHelper::getTransactionResultFromPostVariables($aPostVariables, $trTransactionResult, $HashDigest, $OutputMessage, 'hosted')) {
      $boErrorOccurred = true;
      $ValidateErrorMessage = $OutputMessage;
    } else {
      // now need to validate the hash digest
      $StringToHash = PayzoneHelper::generateStringToTransactionResult($MerchantID, $Password, $trTransactionResult, $PreSharedKey, $HashMethod);

      $CalculatedHashDigest = PayzoneHelper::calculateHashDigest($StringToHash, $PreSharedKey, $HashMethod);
      // does the calculated hash match the one that was passed?
      if (strToUpper($HashDigest) != strToUpper($CalculatedHashDigest)) {
        $boErrorOccurred = true;
        $ValidateErrorMessage = "Hash digests don't match - possible variable tampering";
      } else {
        $boErrorOccurred = false;
      }
    }
    return (!$boErrorOccurred);
  }

  /**
   * [validateTransactionResultTransparent Validation for transaprent redirect results]
   * @method validateTransactionResultTransparent
   * @param  [String]                               $MerchantID
   * @param  [String]                               $Password
   * @param  [String]                               $PreSharedKey
   * @param  [String]                               $HashMethod
   * @param  [Array]                                $aPostVariables
   * @param  [Object]  TransactionResult            $trTransactionResult
   * @param  [String]                               $ValidateErrorMessage
   * @return [Boolean]                              [false on error]
   */
  public static function validateTransactionResultTransparent($MerchantID, $Password, $PreSharedKey, $HashMethod, $aPostVariables, &$trTransactionResult, &$ValidateErrorMessage)
  {
    $ValidateErrorMessage = "";
    $trTransactionResult = null;
    // read the transaction result variables from the post variable list
    if (!PayzoneHelper::getTransactionResultFromPostVariables($aPostVariables, $trTransactionResult, $HashDigest, $OutputMessage, 'transparent')) {
      $boErrorOccurred = true;
      $ValidateErrorMessage = $OutputMessage;
    }
    else {
      // now need to validate the hash digest
      $StringToHash = PayzoneHelper::generateStringToHashPaymentComplete($trTransactionResult, $HashMethod, $PreSharedKey, $MerchantID, $Password);

      $CalculatedHashDigest = PayzoneHelper::calculateHashDigest($StringToHash, $PreSharedKey, $HashMethod);
      // does the calculated hash match the one that was passed?
      if (strToUpper($HashDigest) != strToUpper($CalculatedHashDigest)) {
        $boErrorOccurred = true;
        $ValidateErrorMessage = "Hash digests don't match - possible variable tampering";
      }
      else {
        $boErrorOccurred = false;
      }
    }
    return (!$boErrorOccurred);
  }


  /**
   * [validateTransactionResultHostedServerPull Validation for hosted form server pull transaction ]
   * @method validateTransactionResultHostedServerPull
   * @param  [String]                                    $MerchantID
   * @param  [String]                                    $Password
   * @param  [String]                                    $PreSharedKey
   * @param  [String]                                    $HashMethod
   * @param  [Array]                                     $aQueryStringVariables
   * @param  [String]                                    $PaymentFormResultHandlerURL
   * @param  [Object] TransactionResult                  $trTransactionResult
   * @param  [String]                                    $ValidateErrorMessage
   * @return [Boolean]                                    [False on error]
   */
  public static function validateTransactionResultHostedServerPull($MerchantID, $Password, $PreSharedKey, $HashMethod, $aQueryStringVariables, $PaymentFormResultHandlerURL, &$trTransactionResult, &$ValidateErrorMessage)
  {
    $ValidateErrorMessage = "";
    $trTransactionResult = null;
    // read the transaction reference variables from the query string variable list
    if (!PayzoneHelper::getTransactionReferenceFromQueryString($aQueryStringVariables, $CrossReference, $OrderID, $HashDigest, $OutputMessage)) {
      $boErrorOccurred = true;
      $ValidateErrorMessage = $OutputMessage;
    }
    else {
      // now need to validate the hash digest
      $StringToHash = PayzoneHelper::generateStringToHashQueryString($MerchantID, $Password, $CrossReference, $OrderID, $PreSharedKey, $HashMethod);
      $CalculatedHashDigest = PayzoneHelper::calculateHashDigest($StringToHash, $PreSharedKey, $HashMethod);
      // does the calculated hash match the one that was passed?
      if (strToUpper($HashDigest) != strToUpper($CalculatedHashDigest)) {
        $boErrorOccurred = true;
        $ValidateErrorMessage = "Hash digests don't match - possible variable tampering";
      }
      else {
        // use the cross reference and/or the order ID to pull the
        // transaction results out of storage
        if (!PayzoneHelper::getTransactionResultFromPaymentFormHandler($PaymentFormResultHandlerURL, $MerchantID, $Password, $CrossReference, $trTransactionResult, $OutputMessage)) {
          $ValidateErrorMessage = "Error querying transaction result [" . $CrossReference . "] from [" . $PaymentFormResultHandlerURL . "]: " . $OutputMessage;
          $boErrorOccurred = true;
        }
        else {
          $boErrorOccurred = false;
        }
      }
    }
    return (!$boErrorOccurred);
  }
  /**
   * [getTransactionResultFromPaymentFormHandler Access payment form handler to retrieve results ]
   * @method getTransactionResultFromPaymentFormHandler
   * @param  [String]                                     $PaymentFormResultHandlerURL
   * @param  [String]                                     $MerchantID
   * @param  [String]                                     $Password
   * @param  [String]                                     $CrossReference
   * @param  [Object]TransactionResult                    $trTransactionResult
   * @param  [String]                                     $OutputMessage
   * @return [Boolean]                                     [False on error]
   */
  public static function getTransactionResultFromPaymentFormHandler($PaymentFormResultHandlerURL, $MerchantID, $Password, $CrossReference, &$trTransactionResult, &$OutputMessage)
  {
    $OutputMessage = "";
    $trTransactionResult = null;
    try {
      // use curl to post the cross reference to the
      // payment form to query its status
      $cCURL = curl_init();

      // build up the post string
      $PostString = "MerchantID=" . urlencode($MerchantID) . "&Password=" . urlencode($Password) . "&CrossReference=" . urlencode($CrossReference);
      curl_setopt($cCURL, CURLOPT_URL, $PaymentFormResultHandlerURL);
      curl_setopt($cCURL, CURLOPT_POST, true);
      curl_setopt($cCURL, CURLOPT_POSTFIELDS, $PostString);
      curl_setopt($cCURL, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($cCURL, CURLOPT_SSL_VERIFYPEER, true);
      //check if a certificate list is specified in the php.ini file, otherwise use the bundled one
      $caInfoSetting = ini_get("curl.cainfo");
      if (empty($caInfoSetting)) {
        curl_setopt($cCURL, CURLOPT_CAINFO, __DIR__ . "/../gateway/cacert.pem");
      }
      // read the response
      $Response = curl_exec($cCURL);

      //$ErrorNo = curl_errno($cCURL);
      //$ezErrorMsg = curl_error($cCURL);
      //$HeaderInfo = curl_getinfo($cCURL);
      curl_close($cCURL);
      if ($Response == "") {
        $boErrorOccurred = true;
        $OutputMessage = "Received empty response from payment form handler";
      }
      else {
        try {
          // parse the response into an array
          $aParsedPostVariables = PayzoneHelper::parseNameValueStringIntoArray($Response, true);
          if (!isset($aParsedPostVariables["StatusCode"]) or intval($aParsedPostVariables["StatusCode"]) != 0) {
            $boErrorOccurred = true;
            // the message field is expected if the status code is non-zero
            if (!isset($aParsedPostVariables["Message"])) {
              $OutputMessage = "Received invalid response from payment form handler [" . $Response . "]";
            }
            else {
              $OutputMessage = $aParsedPostVariables["Message"];
            }
          }
          else {
            // status code is 0, so	get the transaction result
            if (!isset($aParsedPostVariables["TransactionResult"])) {
              $boErrorOccurred = true;
              $OutputMessage = "No transaction result in response from payment form handler [" . $Response . "]";
            }
            else {

              // parse the URL decoded transaction result field into a name value array
              $aTransactionResultArray = PayzoneHelper::parseNameValueStringIntoArray(urldecode($aParsedPostVariables["TransactionResult"]), false);
              // parse this array into a transaction result object
              if (!PayzoneHelper::getTransactionResultFromPostVariables($aTransactionResultArray, $trTransactionResult, $HashDigest, $ErrorMessage, 'hosted')) {
                $boErrorOccurred = true;
                $OutputMessage =
                "Error [" .
                $ErrorMessage .
                "] parsing transaction result [" .
                urldecode($aParsedPostVariables["TransactionResult"]) .
                "] in response from payment form handler [" .
                $Response .
                "]";
              }
              else {
                $boErrorOccurred = false;
              }
            }
          }
        } catch (Exception $e) {
          $boErrorOccurred = true;
          $OutputMessage = "Exception [" . $e->getMessage() . "] when processing response from payment form handler [" . $Response . "]";
        }
      }
    } catch (Exception $e) {
      $boErrorOccurred = true;
      $OutputMessage = $e->getMessage();
    }
    return (!$boErrorOccurred);
  }

  /* ############################################## */
  /*  */
  /* ############################################## */
}


/* ############################################## */
/* List Item Class control */
/* ############################################## */
/**
 * [ListItemList List item control class for generating list items for adding to HTML docs ]
 */
class ListItemList
{
  private $m_lilListItemList;
  public function getCount()
  {
    return count($this->m_lilListItemList);
  }
  public function getAt($nIndex)
  {
    if ($nIndex < 0 ||
    $nIndex >= count($this->m_lilListItemList)) {
      throw new Exception('Array index out of bounds');
    }
    return $this->m_lilListItemList[$nIndex];
  }
  public function add($Name, $Value, $boIsSelected)
  {
    $liListItem = new ListItem($Name, $Value, $boIsSelected);
    $this->m_lilListItemList[] = $liListItem;
  }
  /**
   * [toString Convert value to string]
   * @method toString
   * @return [String]   [list item to string]
   */
  public function toString()
  {
    $ReturnString = "";
    for ($nCount = 0; $nCount < count($this->m_lilListItemList); $nCount++) {
      $liListItem = $this->m_lilListItemList[$nCount];
      $ReturnString = $ReturnString."<option";
      if ($liListItem->getValue() != null && $liListItem->getValue() != "") {
        $ReturnString = $ReturnString." value=\"".$liListItem->getValue()."\"";
      }
      if ($liListItem->getIsSelected() == true) {
        $ReturnString = $ReturnString." selected=\"selected\"";
      }
      $ReturnString = $ReturnString.">".$liListItem->getName()."</option>\n";
    }
    return ($ReturnString);
  }
  //constructor
  public function __construct()
  {
    $this->m_lilListItemList = array();
  }
}
/**
 * [ListItem individual list items for adding to list item list]
 */
class ListItem
{
  private $m_Name;
  private $m_Value;
  private $m_boIsSelected;
  //public properties
  public function getName()
  {
    return $this->m_Name;
  }
  public function getValue()
  {
    return $this->m_Value;
  }
  public function getIsSelected()
  {
    return $this->m_boIsSelected;
  }
  //constructor
  public function __construct($Name, $Value, $boIsSelected)
  {
    $this->m_Name = $Name;
    $this->m_Value = $Value;
    $this->m_boIsSelected = $boIsSelected;
  }
}

/* ############################################## */
/* end of List Item Class control */
/* ############################################## */



/* ############################################## */
/* Transaction Result Object */
/* ############################################## */
/**
 * [TransactionResult Transaction Result object for creating and retrieving transaction results]
 */
class TransactionResult
{
  private $m_nStatusCode;
  private $m_Message;
  private $m_nPreviousStatusCode;
  private $m_PreviousMessage;
  private $m_CrossReference;
  private $m_AddressNumericCheckResult;
  private $m_PostCodeCheckResult;
  private $m_CV2CheckResult;
  private $m_ThreeDSecureAuthenticationCheckResult;
  private $m_CardType;
  private $m_CardClass;
  private $m_CardIssuer;
  private $m_nCardIssuerCountryCode;
  private $m_nAmount;
  private $m_nCurrencyCode;
  private $m_OrderID;
  private $m_TransactionType;
  private $m_TransactionDateTime;
  private $m_OrderDescription;
  private $m_CustomerName;
  private $m_Address1;
  private $m_Address2;
  private $m_Address3;
  private $m_Address4;
  private $m_City;
  private $m_State;
  private $m_PostCode;
  private $m_nCountryCode;
  private $m_EmailAddress;
  private $m_PhoneNumber;

  private $m_szMerchantID;
  private $m_szACSURL;
  private $m_szPaREQ;
  private $m_szCallbackUrl;
  private $m_szPaRES;


  public function getStatusCode()
  {
    return $this->m_nStatusCode;
  }

  public function setStatusCode($nStatusCode)
  {
    $this->m_nStatusCode = $nStatusCode;
  }

  public function getMessage()
  {
    return $this->m_Message;
  }

  public function setMessage($Message)
  {
    $this->m_Message = $Message;
  }

  public function getPreviousStatusCode()
  {
    return $this->m_nPreviousStatusCode;
  }

  public function setPreviousStatusCode($nPreviousStatusCode)
  {
    $this->m_nPreviousStatusCode = $nPreviousStatusCode;
  }

  public function getPreviousMessage()
  {
    return $this->m_PreviousMessage;
  }

  public function setPreviousMessage($PreviousMessage)
  {
    $this->m_PreviousMessage = $PreviousMessage;
  }

  public function getCrossReference()
  {
    return $this->m_CrossReference;
  }

  public function setCrossReference($CrossReference)
  {
    $this->m_CrossReference = $CrossReference;
  }

  public function getAddressNumericCheckResult()
  {
    return $this->m_AddressNumericCheckResult;
  }

  public function setAddressNumericCheckResult($AddressNumericCheckResult)
  {
    $this->m_AddressNumericCheckResult = $AddressNumericCheckResult;
  }

  public function getPostCodeCheckResult()
  {
    return $this->m_PostCodeCheckResult;
  }

  public function setPostCodeCheckResult($PostCodeCheckResult)
  {
    $this->m_PostCodeCheckResult = $PostCodeCheckResult;
  }

  public function getCV2CheckResult()
  {
    return $this->m_CV2CheckResult;
  }

  public function setCV2CheckResult($CV2CheckResult)
  {
    $this->m_CV2CheckResult = $CV2CheckResult;
  }

  public function getThreeDSecureAuthenticationCheckResult()
  {
    return $this->m_ThreeDSecureAuthenticationCheckResult;
  }

  public function setThreeDSecureAuthenticationCheckResult($ThreeDSecureAuthenticationCheckResult)
  {
    $this->m_ThreeDSecureAuthenticationCheckResult = $ThreeDSecureAuthenticationCheckResult;
  }

  public function getCardType()
  {
    return $this->m_CardType;
  }

  public function setCardType($CardType)
  {
    $this->m_CardType = $CardType;
  }

  public function getCardClass()
  {
    return $this->m_CardClass;
  }

  public function setCardClass($CardClass)
  {
    $this->m_CardClass = $CardClass;
  }

  public function getCardIssuer()
  {
    return $this->m_CardIssuer;
  }

  public function setCardIssuer($CardIssuer)
  {
    $this->m_CardIssuer = $CardIssuer;
  }

  public function getCardIssuerCountryCode()
  {
    return $this->m_nCardIssuerCountryCode;
  }

  public function setCardIssuerCountryCode($nCardIssuerCountryCode)
  {
    $this->m_nCardIssuerCountryCode = $nCardIssuerCountryCode;
  }

  public function getAmount()
  {
    return $this->m_nAmount;
  }

  public function setAmount($nAmount)
  {
    $this->m_nAmount = $nAmount;
  }

  public function getCurrencyCode()
  {
    return $this->m_nCurrencyCode;
  }

  public function setCurrencyCode($nCurrencyCode)
  {
    $this->m_nCurrencyCode = $nCurrencyCode;
  }

  public function getOrderID()
  {
    return $this->m_OrderID;
  }

  public function setOrderID($OrderID)
  {
    $this->m_OrderID = $OrderID;
  }

  public function getTransactionType()
  {
    return $this->m_TransactionType;
  }

  public function setTransactionType($TransactionType)
  {
    $this->m_TransactionType = $TransactionType;
  }

  public function getTransactionDateTime()
  {
    return $this->m_TransactionDateTime;
  }

  public function setTransactionDateTime($TransactionDateTime)
  {
    $this->m_TransactionDateTime = $TransactionDateTime;
  }

  public function getOrderDescription()
  {
    return $this->m_OrderDescription;
  }

  public function setOrderDescription($OrderDescription)
  {
    $this->m_OrderDescription = $OrderDescription;
  }

  public function getCustomerName()
  {
    return $this->m_CustomerName;
  }

  public function setCustomerName($CustomerName)
  {
    $this->m_CustomerName = $CustomerName;
  }

  public function getAddress1()
  {
    return $this->m_Address1;
  }

  public function setAddress1($Address1)
  {
    $this->m_Address1 = $Address1;
  }

  public function getAddress2()
  {
    return $this->m_Address2;
  }

  public function setAddress2($Address2)
  {
    $this->m_Address2 = $Address2;
  }

  public function getAddress3()
  {
    return $this->m_Address3;
  }

  public function setAddress3($Address3)
  {
    $this->m_Address3 = $Address3;
  }

  public function getAddress4()
  {
    return $this->m_Address4;
  }

  public function setAddress4($Address4)
  {
    $this->m_Address4 = $Address4;
  }

  public function getCity()
  {
    return $this->m_City;
  }

  public function setCity($City)
  {
    $this->m_City = $City;
  }

  public function getState()
  {
    return $this->m_State;
  }

  public function setState($State)
  {
    $this->m_State = $State;
  }

  public function getPostCode()
  {
    return $this->m_PostCode;
  }

  public function setPostCode($PostCode)
  {
    $this->m_PostCode = $PostCode;
  }

  public function getCountryCode()
  {
    return $this->m_nCountryCode;
  }

  public function setCountryCode($nCountryCode)
  {
    $this->m_nCountryCode = $nCountryCode;
  }

  public function getEmailAddress()
  {
    return $this->m_EmailAddress;
  }

  public function setEmailAddress($emailAddress)
  {
    $this->m_EmailAddress = $emailAddress;
  }

  public function getPhoneNumber()
  {
    return $this->m_PhoneNumber;
  }

  public function setPhoneNumber($phoneNumber)
  {
    $this->m_PhoneNumber = $phoneNumber;
  }
  public function setMerchantID($MerchantID)
  {
    $this -> m_szMerchantID = $MerchantID;
  }

  public function getMerchantID()
  {
    return $this -> m_szMerchantID;
  }

  public function setACSURL($ACSURL)
  {
    $this -> m_szACSURL = $ACSURL;
  }

  public function getACSURL()
  {
    return $this -> m_szACSURL;
  }

  public function setPaREQ($PaREQ)
  {
    $this -> m_szPaREQ = $PaREQ;
  }

  public function getPaREQ()
  {
    return $this -> m_szPaREQ;
  }

  public function setCallbackUrl($CallbackUrl)
  {
    $this -> m_szCallbackUrl = $CallbackUrl;
  }

  public function getCallbackURL()
  {
    return $this -> m_szCallbackUrl;
  }

  public function setPaRES($PaRES)
  {
    $this -> m_szPaRES = $PaRES;
  }

  public function getPaRES()
  {
    return $this -> m_szPaRES;
  }
}

/* ############################################## */
/* End of transaction result object */
/* ############################################## */
