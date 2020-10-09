<?php

namespace LoveyCom\CashFree\PaymentGateway;

use LoveyCom\CashFree\HttpClient\HttpClient;
use LoveyCom\CashFree\Util\ActivityLogger;

class Order extends PaymentGatewayBase
{
    /**
     * This is an array of data to be sent to the server
     *
     * @var array
     */
    protected $params;

    /**
     * This is an array of headers to be sent to the server
     *
     * @var array
     */
    protected $header;

    public function __construct()
    {
        $this->header = [
            'Content-Type: application/x-www-form-urlencoded'
        ];
        //dd(PaymentGatewayBase::getTestURL());
        $this->params = ["appId" => PaymentGatewayBase::getAppID(), "secretKey" => PaymentGatewayBase::getSecretKey()];
    }

    /**
     * Create an order
     *
     * @param array $order
     * @return object
     */
    public function create($order)
    {
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }

        $url = $apiEndpoint . "/api/v1/order/create";

        $this->params = array_merge($this->params, $order);

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        if ($response->status == 'ERROR')
            ActivityLogger::Log(2, "Creating New Order Failed: ", (array) $response, __FILE__);

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

    /**
     * Get payment link for an already created order
     *
     * @param string $orderId
     * @return object
     */
    public function getLink($orderId)
    {
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }
        $url = $apiEndpoint . "/api/v1/order/info/link";

        $this->params['orderId'] = $orderId;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        if ($response->status == 'ERROR')
            ActivityLogger::Log(2, "Getting Order Link Failed: ", (array) $response, __FILE__);

        return $response;
    }

    /**
     * Get an order details
     *
     * @param string $orderId
     * @return object
     */
    public function getDetails($orderId)
    {
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }
        $url = $apiEndpoint . "/api/v1/order/info/";

        $this->params['orderId'] = $orderId;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        if ($response->status == 'ERROR')
            ActivityLogger::Log(2, "Getting Order Details Failed: ", (array) $response, __FILE__);

        return $response;
    }

    /**
     * Get the status of an order
     *
     * @param string $orderId
     * @return object
     */
    public function getStatus($orderId)
    {
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }
        $url = $apiEndpoint . "/api/v1/order/info/status";

        $this->params['orderId'] = $orderId;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        if ($response->status == 'ERROR')
            ActivityLogger::Log(2, "Checking Order Status Failed: ", (array) $response, __FILE__);

        return $response;
    }
}
