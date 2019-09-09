<?php

namespace LoveyCom\CashFree\PaymentGateway;

use Illuminate\Support\Facades\Config;

class PaymentGatewayBase
{
    public static $testURL;
    public static $prodURL;
    public static $appID;
    public static $secretKey;

    public static function setAppID($appID = "")
    {
        if ($appID == "") {
            self::$appID = Config::get('cashfree.PG.appID');
        } else {
            self::$appID = $appID;
        }
    }

    public static function getAppID()
    {
        if (self::$appID != null) {
            return self::$appID;
        }
        self::setAppID();
        return self::$appID;
    }

    public static function setSecretKey($secretKey = "")
    {
        if ($secretKey == "") {
            self::$secretKey = Config::get('cashfree.PG.secretKey');
        } else {
            self::$secretKey = $secretKey;
        }
    }

    public static function getSecretKey()
    {
        if (self::$secretKey != null) {
            return self::$secretKey;
        }
        self::setSecretKey();
        return self::$secretKey;
    }

    public static function setTestURL($testURL = "")
    {
        if ($testURL == "") {
            self::$testURL = Config::get('cashfree.PG.testURL');
        } else {
            self::$testURL = $testURL;
        }
    }

    public static function getTestURL()
    {
        if (self::$testURL != null) {
            return self::$testURL;
        }
        self::setTestURL();
        return self::$testURL;
    }

    public static function setProdURL($prodURL = "")
    {
        if ($prodURL == "") {
            self::$prodURL = Config::get('cashfree.PG.prodURL');
        } else {
            self::$prodURL = $prodURL;
        }
    }

    public static function getProdURL()
    {
        if (self::$prodURL != null) {
            return self::$prodURL;
        }
        self::setProdURL();
        return self::$prodURL;
    }
}
