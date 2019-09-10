<?php

namespace LoveyCom\CashFree\PaymentGateway;

use Illuminate\Support\Facades\Config;

class PaymentGatewayBase
{
    /**
     * Variable for holding API Test URL
     *
     * @var string
     */
    public static $testURL;

    /**
     * Variable for holding API PRODUCTION/LIVE URL
     *
     * @var string
     */
    public static $prodURL;

    /**
     * Variable for holding APP ID
     *
     * @var string
     */
    public static $appID;

    /**
     * Variable for holding APP SECRET KEY
     *
     * @var string
     */
    public static $secretKey;

    /**
     * Sets the APPID either set when passed as argument or fetched from config
     *
     * @param string $appID
     * @return void
     */
    public static function setAppID($appID = "")
    {
        if ($appID == "") {
            self::$appID = Config::get('cashfree.PG.appID');
        } else {
            self::$appID = $appID;
        }
    }

    /**
     * Get APPID
     *
     * @return string
     */
    public static function getAppID()
    {
        if (self::$appID != null) {
            return self::$appID;
        }
        self::setAppID();
        return self::$appID;
    }

    /**
     * Sets the APPSECRET either set when passed as argument or fetched from config
     *
     * @param string $secretKey
     * @return void
     */
    public static function setSecretKey($secretKey = "")
    {
        if ($secretKey == "") {
            self::$secretKey = Config::get('cashfree.PG.secretKey');
        } else {
            self::$secretKey = $secretKey;
        }
    }

    /**
     * GET APPSECRET
     *
     * @return string
     */
    public static function getSecretKey()
    {
        if (self::$secretKey != null) {
            return self::$secretKey;
        }
        self::setSecretKey();
        return self::$secretKey;
    }

    /**
     * Set Test URL for the API either by passing it as argument or fetching from config.
     *
     * @param string $testURL
     * @return void
     */
    public static function setTestURL($testURL = "")
    {
        if ($testURL == "") {
            self::$testURL = Config::get('cashfree.PG.testURL');
        } else {
            self::$testURL = $testURL;
        }
    }

    /**
     * Get TEST URL
     *
     * @return string
     */
    public static function getTestURL()
    {
        if (self::$testURL != null) {
            return self::$testURL;
        }
        self::setTestURL();
        return self::$testURL;
    }

    /**
     * Set PRODUCTION/LIVE URL for the API either by passing it as argument or fetching from config.
     *
     * @param string $prodURL
     * @return void
     */
    public static function setProdURL($prodURL = "")
    {
        if ($prodURL == "") {
            self::$prodURL = Config::get('cashfree.PG.prodURL');
        } else {
            self::$prodURL = $prodURL;
        }
    }

    /**
     * GET PRODUCTION URL
     *
     * @return void
     */
    public static function getProdURL()
    {
        if (self::$prodURL != null) {
            return self::$prodURL;
        }
        self::setProdURL();
        return self::$prodURL;
    }

    /**
     * Check production status from the config.
     * @return boolean
     */
    public static function isProduction()
    {
        if (Config::get('cashfree.PG.isLive') === true) {
            return true;
        } else {
            return false;
        }
    }
}
