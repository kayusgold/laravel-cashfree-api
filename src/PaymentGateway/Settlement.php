<?php

namespace LoveyCom\CashFree\PaymentGateway;

class Settlement extends PaymentGatewayBase
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
     * Get all settlements based on date range
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $lastId Optional
     * @param string $count Optional
     * @return object
     */
    public function getAll($startDate, $endDate, $lastId = "", $count = "")
    {
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }
        $url = $apiEndpoint . "/api/v1/settlements";

        $this->params['startDate'] = $startDate;
        $this->params['endDate'] = $endDate;
        if ($lastId != "")
            $this->params['lastId'] = $lastId;
        if ($count != "")
            $this->params['count'] = $count;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        if ($response->status == 'ERROR')
            ActivityLogger::Log(2, "Getting All Settlement Failed: ", (array) $response, __FILE__);

        return $response;
    }

    /**
     * Get a single settlement details
     *
     * @param string $settlementId
     * @return object
     */
    public function getOne($settlementId)
    {
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }
        $url = $apiEndpoint . "/api/v1/settlement";

        $this->params['settlementId'] = $settlementId;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        if ($response->status == 'ERROR')
            ActivityLogger::Log(2, "Fetching Settlement Failed: ", (array) $response, __FILE__);

        return $response;
    }
}
