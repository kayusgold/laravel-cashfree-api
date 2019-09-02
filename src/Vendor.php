<?php

namespace LoveyCom\CashFree;

use LoveyCom\CashFree\HttpClient\HttpClient;
use Illuminate\Support\Facades\Config;

class Vendor
{
    protected $token;
    protected $header;

    public function __construct()
    {
        if (CashFree::getToken() == null || CashFree::verifyToken == false)
            $this->token = CashFree::authenticate();

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

        dd($response);

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
        dd($response);
        if ($response->status == "SUCCESS") {
            return $response;
        }

        return (object) [];
    }
}
