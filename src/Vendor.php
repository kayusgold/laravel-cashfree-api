<?php

namespace LoveyCom\CashFree;

use LoveyCom\CashFree\HttpClient\HttpClient;
use Illuminate\Support\Facades\Config;
use LoveyCom\CashFree\Util\ActivityLogger;

class Vendor
{
    protected $token;
    protected $header;

    public function __construct()
    {
        if (CashFree::getToken() == null || CashFree::verifyToken() == false) {
            $this->token = CashFree::authenticate();
        } else {
            $this->token = CashFree::getToken();
        }

        $this->header = [
            'Authorization: Bearer ' . $this->token
        ];
    }

    /**
     * This function does 2 things:
     * 1. create a vendor if vendorId is not passed
     * 2. update a vendor if vendorId is passed.
     *
     * @param array $vendor
     * @param string $vendorId
     * @return object HTTP Response
     */
    public function create($vendor = [], $vendorId = "")
    {

        //make create request.
        $params = array(
            'vendorId' => $vendor['vID'],
            'name' => $vendor['name'],
            'phone' => $vendor['phone'],
            'email' => $vendor['email'],
            'commission' => $vendor['commission'],
            'bankAccount' => $vendor['bankAccount'],
            'accountHolder' => $vendor['accountHolder'],
            'ifsc' => $vendor['ifsc'],
            'address1' => $vendor['address1'],
            'address2' => $vendor['address2'],
            'city' => $vendor['city'],
            'state' => $vendor['state'],
            'pincode' => $vendor['pincode'],
        );

        $client = new HttpClient();

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com');
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com');
        }

        if ($vendorId != "") {
            $url .= "/ces/v1/editVendor/$vendorId"; //update vendor
        } else {
            $url .= "/ces/v1/addVendor"; //create vendor
        }

        $response = $client->request('POST', $url, $this->header, $params);

        //dd($response);

        return $response;
    }

    public function retreive($vendorId = "")
    {
        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com');
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com');
        }

        if ($vendorId != "") {
            $url .= "/ces/v1/getVendor/$vendorId";
        } else {
            $url .= "/ces/v1/getVendors";
        }

        $client = new HttpClient();

        $response = $client->request('GET', $url, $this->header);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Retreiving Vendor Details Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Check Vendor Creation Status
     *
     * @param string $vendorId
     * @return object
     */
    public function checkStatus($vendorId)
    {
        $vendor = $this->retreive($vendorId);
        //dd($vendor);
        if (blank($vendor)) {
            $vendor = $vendor->data->status;
        } else {
            $vendor = "Unknown";
        }
        return (object) ['vendor' => $vendorId, 'status' => $vendor];
    }

    /**
     * This allows you to adjust vendor balance
     *
     * @param string $vendorId
     * @param string $adjustmentId
     * @param string $amount
     * @param string $type it can be CREDIT or DEBIT. CREDIT is the default.
     * @param string $remark
     * @return object
     */
    public function adjustVendorBalance($vendorId, $adjustmentId, $amount, $type = "CREDIT", $remark = "")
    {
        //make create request.
        $params = ["vendorId" => $vendorId, "amount" => $amount, "type" => $type, "adjustmentId" => $adjustmentId, "remark" => $remark];

        $client = new HttpClient();

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/adjustVendor";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/adjustVendor";
        }

        $response = $client->request('POST', $url, $this->header, $params);

        //dd($response);

        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Adjust Vendor Balance Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Allows you to request settlement to vendor manually. Please note that settlement might not happen immediately.
     *
     * @param string $vendorId
     * @param string $amount
     * @return object
     */
    public function requestVendorPayout($vendorId, $amount)
    {
        $params = ["vendorId" => $vendorId, "amount" => $amount];

        $client = new HttpClient();

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/requestVendorPayout";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/requestVendorPayout";
        }

        $response = $client->request('POST', $url, $this->header, $params);

        //dd($response);

        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Vendor Payout Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Get the vendor ledger for a vendor which shows all changes to vendor balance.
     *
     * @param string $vendorId
     * @param int $maxReturn Optional
     * @param int $lastReturnId Optional
     * @return object
     */
    public function getLedger($vendorId, $maxReturn = 50, $lastReturnId = "")
    {
        $client = new HttpClient();

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/getVendorLedger/$vendorId?maxReturn=$maxReturn";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/getVendorLedger/$vendorId?maxReturn=$maxReturn";
        }

        if ($lastReturnId != "") {
            $url = $url . "&lastReturnId=$lastReturnId";
        }

        $response = $client->request('GET', $url, $this->header);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Get Vendor Ledger Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Get the vendor transfer if $vendorTransferId is set or leave it empty and set vendorId to get the vendor ledger for a vendor which shows all changes to vendor balance.
     *
     * @param string $vendorTransferId Do not set if you want to get all transfers but set if you need a particular transfer
     * @param string $vendorId Do not set if you need a particular transfer details, but set if you want to get all transfers
     * @param integer $maxReturn Optional
     * @param string $lastReturnId Optional
     * @param string $startDate Optional
     * @param string $endDate Optional
     * @return object
     */
    public function getTransferDetails($vendorTransferId = "", $vendorId = "", $maxReturn = 50, $lastReturnId = "", $startDate = "", $endDate = "")
    {
        $client = new HttpClient();

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com');
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com');
        }

        if ($vendorTransferId == "" && $vendorId != "") {
            $url = $url . "/ces/v1/getVendorTransfers/$vendorId";
        } else if ($vendorTransferId != "" && $vendorId == "") {
            $url = $url . "/ces/v1/getVendorTransfer/$vendorTransferId?maxReturn=$maxReturn";
            if ($lastReturnId != "")
                $url .=  "&lastReturnId=$lastReturnId";
            if ($startDate != "")
                $url .= "&startDate=$startDate";
            if ($endDate != "")
                $url .= "&endDate=$endDate";
        } else {
            return (object) ['status' => 'ERROR', 'message' => 'Required parameters were not set. Please read documentation.'];
        }

        $response = $client->request('GET', $url, $this->header);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Get Transfer Details Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }

    /**
     * Transfer from one vendor to another
     *
     * @param string $fromVendorId
     * @param string $toVendorId
     * @param string $amount
     * @param string $adjustmentId
     * @return object
     */
    public function transferBetweenVendors($fromVendorId, $toVendorId, $amount, $adjustmentId)
    {
        $client = new HttpClient();

        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/transferVendorBalance";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/transferVendorBalance";
        }

        $params = ["fromVendorId" => $fromVendorId, "toVendorId" => $toVendorId, "adjustmentId" => $adjustmentId, "amount" => $amount];

        $response = $client->request('POST', $url, $this->header, $params);
        //dd($response);
        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Check Balance Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }
}
