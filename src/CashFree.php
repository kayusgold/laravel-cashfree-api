<?php

namespace LoveyCom\CashFree;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use LoveyCom\CashFree\HttpClient\HttpClient;
use LoveyCom\CashFree\Util\Singleton;

class CashFree extends Singleton
{

    public static $testURL;

    public static $prodURL;

    public static $appID;

    public static $secretKey;

    // @var int Maximum number of request retries
    public static $maxNetworkRetries = 0;

    // @var boolean Whether client telemetry is enabled. Defaults to true.
    public static $enableTelemetry = true;

    // @var float Maximum delay between retries, in seconds
    private static $maxNetworkRetryDelay = 2.0;

    // @var float Initial delay between retries, in seconds
    private static $initialNetworkRetryDelay = 0.5;

    private static $isProduction = false;

    private static $instance;

    private static $client;

    private static $token;

    // public static function getInstance()
    // {
    //     // Check is $_instance has been set
    //     if (!isset(self::$instance)) {
    //         // Creates sets object to instance
    //         self::$instance = new CashFree();
    //     }

    //     // Returns the instance
    //     return self::$instance;
    // }

    /**
     * @param string Uses the values set in the config.
     */
    public static function setAppID()
    {
        self::$appID = Config::get('cashfree.appID');
    }

    /**
     * @return string The App ID used for the connection request.
     */
    public static function getAppID()
    {
        self::setAppID();
        return self::$appID;
    }

    /**
     * @param string Uses the values set in the config.
     */
    public static function setSecretKey()
    {
        self::$secretKey = Config::get('cashfree.secretKey');
    }

    /**
     * @return string The Secret ID used for the connection request.
     */
    public static function getSecretKey()
    {
        self::setSecretKey();
        return self::$secretKey;
    }

    public static function getToken()
    {
        return self::$token;
    }

    /**
     * @return string The CashFree Production API URL
     */
    public static function getProdURL()
    {
        return Config::get('cashfree.prodURL');
    }

    /**
     * @return string The CashFree Test API URL
     */
    public static function getTestURL()
    {
        return Config::get('cashfree.testURL');
    }

    /**
     * @return int Maximum number of request retries
     */
    public static function getMaxNetworkRetries()
    {
        return self::$maxNetworkRetries;
    }

    /**
     * @param int $maxNetworkRetries Maximum number of request retries
     */
    public static function setMaxNetworkRetries($maxNetworkRetries)
    {
        self::$maxNetworkRetries = $maxNetworkRetries;
    }

    /**
     * @return float Maximum delay between retries, in seconds
     */
    public static function getMaxNetworkRetryDelay()
    {
        return self::$maxNetworkRetryDelay;
    }

    /**
     * @return float Initial delay between retries, in seconds
     */
    public static function getInitialNetworkRetryDelay()
    {
        return self::$initialNetworkRetryDelay;
    }

    public static function setIsProduction($status)
    {
        self::$isProduction = $status;
    }

    public static function getIsProduction()
    {
        return self::$isProduction;
    }

    public static function authenticate()
    {
        $client = new HttpClient();
        $header = [
            'X-Client-Id: ' . self::getAppID(),
            'X-Client-Secret: ' . self::getSecretKey()
        ];

        if (self::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/authorize";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/authorize";
        }

        Log::info('Authenticating.....');
        $response = $client->request('POST', $url, $header);
        //dd($response);
        if (isset($response->status) && $response->status == "SUCCESS") {
            self::$token = $response->data->token;
        }
        return self::$token;
    }

    public static function verifyToken()
    {
        $client = new HttpClient();
        $header = [
            'Authorization: Bearer ' . self::$token
        ];

        if (self::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/verifyToken";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/verifyToken";
        }

        Log::info('Verifying Token.....');
        $response = $client->request('POST', $url, $header);
        if ($response->status == "SUCCESS" && $response->message == "Token is valid") {
            return true;
        }
        return false;
    }
}
