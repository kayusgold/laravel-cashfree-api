<?php

namespace LoveyCom\CashFree;

use Illuminate\Support\Facades\Config;
use LoveyCom\CashFree\HttpClient\HttpClient;
use LoveyCom\CashFree\Util\ActivityLogger;

class Marketplace
{
    protected $token;
    protected $header;
    protected $maxReturn;

    public function __construct()
    {
        if (CashFree::getToken() == null || CashFree::verifyToken() == false) {
            $this->token = CashFree::authenticate();
        } else {
            $this->token = CashFree::getToken();
        }
        $this->maxReturn = Config::get('cashfree.maxReturn', '100');

        $this->header = [
            'Authorization: Bearer ' . $this->token
        ];
        //dd($this->header);
    }

    /**
     * Allows you to check balance of your Marketplace Settlements account.
     *
     * @return object
     */
    public function checkBalance()
    {
        $client = new HttpClient();

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/getBalance";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/getBalance";
        }

        $response = $client->request('GET', $url, $this->header);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Check Balance Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Allows you to withdraw balance from Marketplace Settlements to your bank account.
     *
     * @param [type] $amount
     * @param string $remark
     * @return void
     */
    public function withdraw($amount, $remark = "")
    {
        $params = ["amount" => $amount, "remark" => $remark];

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/requestWithdrawal";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/requestWithdrawal";
        }

        $client = new HttpClient();

        $response = $client->request('POST', $url, $this->header, $params);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Withdraw Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Allows you to check balance of your Marketplace Settlements account.
     *
     * @param string $maxReturn
     * @param string $lastReturnId
     * @return void
     */
    public function getLedger($maxReturn = "", $lastReturnId = "")
    {
        if ($maxReturn == "")
            $maxReturn = $this->maxReturn;

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/getLedger?maxReturn=$maxReturn";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/getLedger?maxReturn=$maxReturn";
        }

        if ($lastReturnId != "")
            $url .= "&lastReturnId=$lastReturnId";

        $client = new HttpClient();

        $response = $client->request('GET', $url, $this->header);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Get Ledger Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }
}
