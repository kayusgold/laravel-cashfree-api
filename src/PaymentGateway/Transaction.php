<?php

namespace LoveyCom\CashFree\PaymentGateway;

class Transaction extends PaymentGatewayBase
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
     * Get all transactions from a date range
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $txStatus
     * @param string $lastID Optional
     * @param string $count Optional
     * @return object
     */
    public function retreive($startDate = "", $endDate = "", $txStatus = "", $lastID = "", $count = "")
    {
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }
        $url = $apiEndpoint . "/api/v1/transactions";

        $this->params['startDate'] = $startDate;
        $this->params['endDate'] = $endDate;
        $this->params['txStatus'] = $txStatus;
        $this->params['lastID'] = $lastID;
        $this->params['count'] = $count;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        if ($response->status == 'ERROR')
            ActivityLogger::Log(2, "Getting All Transactions Failed: ", (array) $response, __FILE__);

        return $response;
    }
}
