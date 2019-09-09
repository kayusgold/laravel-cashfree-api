<?php

namespace LoveyCom\CashFree\PaymentGateway;

use LoveyCom\CashFree\HttpClient\HttpClient;

class Order extends PaymentGatewayBase
{
    protected $params;
    protected $header;

    public function __construct()
    {
        $this->header = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
        //dd(PaymentGatewayBase::getTestURL());
        $this->params = ["appId" => PaymentGatewayBase::getAppID(), "secretKey" => PaymentGatewayBase::getSecretKey()];
    }

    public function create($order)
    {
        $apiEndpoint = PaymentGatewayBase::getTestURL();
        $url = $apiEndpoint . "/api/v1/order/create";

        $this->params = array_merge($this->params, $order);

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        return $response;

        // if ($response->status == "OK") {
        //     $paymentLink = $response->paymentLink;
        //     return $response;
        //     //dd($response);
        //     //Send this payment link to customer over email/SMS OR redirect to this link on browser
        // } else {

        //     dd($response);
        //     //Log request, $jsonResponse["reason"]
        // }
    }

    public function getLink($orderId)
    {
        $apiEndpoint = PaymentGatewayBase::getTestURL();
        $url = $apiEndpoint . "/api/v1/order/info/link";

        $this->params['orderId'] = $orderId;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        return $response;
    }

    public function getDetails($orderId)
    {
        $apiEndpoint = PaymentGatewayBase::getTestURL();
        $url = $apiEndpoint . "/api/v1/order/info/";

        $this->params['orderId'] = $orderId;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        return $response;
    }

    public function getStatus($orderId)
    {
        $apiEndpoint = PaymentGatewayBase::getTestURL();
        $url = $apiEndpoint . "/api/v1/order/info/status";

        $this->params['orderId'] = $orderId;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        return $response;
    }
}
