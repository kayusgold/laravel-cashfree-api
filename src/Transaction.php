<?php

namespace LoveyCom\CashFree;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use LoveyCom\CashFree\HttpClient\HttpClient;
use LoveyCom\CashFree\Util\ActivityLogger;

class Transaction
{
    protected $token;
    protected $header;

    public function __construct()
    {
        Log::debug(CashFree::getToken());
        if (CashFree::getToken() == null || CashFree::verifyToken() == false) {
            $this->token = CashFree::authenticate();
        } else {
            $this->token = CashFree::getToken();
        }

        $this->header = [
            'Authorization: Bearer ' . $this->token
        ];
        //dd($this->header);
    }

    public function importTransaction($details = [])
    {
        $client = new HttpClient();

        // $params = [
        //     'orderId' => $details['orderId'],
        //     'orderAmount' => $details['orderAmount'],
        //     'orderNote' => $details['orderNote'],
        //     'customerName' => $details['customerName'],
        //     'customerPhone' => $details['customerPhone'],
        //     'customerEmail' => $details['customerEmail'],
        //     'vendors' => [
        //         $details['vendors']
        //     ]
        // ];

        $params = $details;

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . '/ces/v1/importTransaction';
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . '/ces/v1/importTransaction';
        }

        $response = $client->request('POST', $url, $this->header, $params);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Import Transaction Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Get Transaction or Transactions.
     * If @param string $orderId is not set, All Transactions will be fetched.
     *
     * @param string $orderId
     * @return void
     */
    public function retreive($orderId = "")
    {
        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com');
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com');
        }

        if ($orderId != "") {
            $url .= "/ces/v1/getTransaction/$orderId";
        } else {
            $url .= "/ces/v1/getTransactions";
        }

        $client = new HttpClient();

        $response = $client->request('GET', $url, $this->header);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Retreive Transaction Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Attach a Vendor to a already created Transaction.
     * Please, only set either commission or commissionAmount and not the two together.
     *
     * @param string $orderId
     * @param string $vendorId
     * @param string $commission in percentage
     * @param string $commissionAmount in rupee.
     * @return object
     */
    public function attachVendorToTransaction($orderId, $vendorId, $commission = "", $commissionAmount = "")
    {
        if ($commission != "" && $commissionAmount == "") {
            $params = ["orderId" => $orderId, "vendorId" => $vendorId, "commission" => $commission];
        } else if ($commissionAmount != "" && $commission == "") {
            $params = ["orderId" => $orderId, "vendorId" => $vendorId, "commission" => $commissionAmount];
        } else {
            $params = ["orderId" => $orderId, "vendorId" => $vendorId, "commission" => $commission];
        }

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/attachVendor";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/attachVendor";
        }

        $client = new HttpClient();

        $response = $client->request('POST', $url, $this->header, $params);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Attach Vendor Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Detach a Vendor from a already created Transaction.
     *
     * @param string $orderId
     * @param string $vendorId
     * @return object
     */
    public function detachVendorFromTransaction($orderId, $vendorId)
    {

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/detachVendor";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/detachVendor";
        }

        $client = new HttpClient();

        $params = ["orderId" => $orderId, "vendorId" => $vendorId];

        $response = $client->request('POST', $url, $this->header, $params);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Detach Vendor from Transaction Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }
}
