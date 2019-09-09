<?php

namespace LoveyCom\CashFree\Util;

use LoveyCom\CashFree\CashFree;

class APIAuthentication
{
    public static $apiKey;
    public static $secretKey;
    public static $isProduction = true;

    public static function Authenticate() {
        $a = CashFree::$appID;
    }
}
