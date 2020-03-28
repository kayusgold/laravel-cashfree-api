<?php

return [
    //These are for the Marketplace
    'appID' => '',
    'secretKey' => '',
    'testURL' => 'https://ces-gamma.cashfree.com',
    'prodURL' => 'https://ces-api.cashfree.com',
    'maxReturn' => 100, //this is for request pagination
    'isLive' => false,

    //For the PaymentGateway.
    'PG' => [
        'appID' => '',
        'secretKey' => '',
        'testURL' => 'https://test.cashfree.com',
        'prodURL' => 'https://api.cashfree.com',
        'isLive' => false
    ]
];
