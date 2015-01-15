<?php

use winzou\Limonetik\APIClient;
use Buzz\Client\Curl;

require '../vendor/autoload.php';

$curl = new Curl;

// Uncomment this if you're facing some issues on Windows -- for development purpose only!
// $curl->setVerifyPeer(false);

$api = new APIClient(
    array(
      'merchantId' => 'your-merchant-id',
      'key'        => 'your-key',
      'sandbox'    => true
    ), $curl
);

$paymentOrder = $api->PaymentOrderCreate(json_decode(
    '{
    "MerchantId": "citronrose-fr",
    "PaymentPageId": "sacarte",
    "Amount": 95.0,
    "Currency": "EUR",
    "MerchantUrls": {
      "ReturnUrl": "http://www.citronrose.com/Payment_Return.aspx",
      "AbortedUrl": "http://www.citronrose.com/Payment_Cancelled.aspx",
      "ErrorUrl": "http://www.citronrose.com/Payment_Error.aspx",
      "ServerNotificationUrl": "http://www.citronrose.com/Payment_Notification.aspx"
    }
  }'
, true));

var_dump($paymentOrder['PaymentOrderId']);
var_dump($api->PaymentOrderDetail($paymentOrder['PaymentOrderId'], array('MerchantUrls')));
//var_dump($api->PaymentOrderCharge($payment['PaymentOrderId'], 50, 'EUR'));
//var_dump($api->PaymentOrderCancel($payment['PaymentOrderId'], 10, 'EUR'));
