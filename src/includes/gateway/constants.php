<?php /**
* Payzone Payment Gateway
* ========================================
* Web:   http://payzone.co.uk
* Email:  online@payzone.com
* Authors: Payzone, Keith Rigby
*/
namespace Payzone\Constants;


if (count(get_included_files()) ==1) {
    exit("Direct access not permitted.");
}

/**
 * [TRANSACTION_TYPE constants for transaction type selection]
 */
class TRANSACTION_TYPE
{
  const SALE            = 'SALE';
  const PREAUTH         = 'PREAUTH';
    const REFUND         = 'REFUND';
}
/**
 * [INTEGRATION_TYPE constants for populating fields / queries]
 */
class INTEGRATION_TYPE
{
  const HOSTED          = 'Hosted Payment Form';
  const DIRECT          = 'Direct API';
  const TRANSPARENT     = 'Transparent Redirect';
}
/**
 * [HASH_METHOD constants for populating fields / queries]
 */
class HASH_METHOD
{
  const SHA1        = 'SHA1';
  const MD5         = 'MD5';
  const HMACSHA1    = 'HMACSHA1';
  const HMACMD5     = 'HMACMD5';
}
/**
 * [RESULT_DELIVERY_METHOD constants for populating fields / queries]
 */
class RESULT_DELIVERY_METHOD
{
  const POST           = 'POST';
  const SERVER_PULL    = 'SERVER_PULL';
}
/**
 * [PAYZONE_RESPONSE_CSS constants for human readable response code mapping]
 */
class PAYZONE_RESPONSE_CSS
{
  const ERROR           = 'payzone-error';
  const SUCCESS         = 'payzone-success';
  const DECLINED        = 'payzone-declined';
  const DUPLICATE       = 'payzone-duplicate';
  const UNKNOWN         = 'payzone-error payzone-unknown';
}
/**
 * [PAYZONE_RESPONSE_OUTCOMES constants for human readable response code mapping]
 */
class PAYZONE_RESPONSE_OUTCOMES
{
  const SUCCESS         = 'Success';
  const DECLINED        = 'Declined';
  const THREED          = '3D Secure Required';
  const DUPLICATE       = 'Duplicate';
  const ERROR           = 'Error';
  const UNKNOWN         = 'Unknown';
}
?>
