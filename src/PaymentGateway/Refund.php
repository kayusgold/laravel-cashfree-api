<?php

namespace LoveyCom\CashFree\PaymentGateway;

use LoveyCom\CashFree\Util\ActivityLogger;

class Refund extends PaymentGatewayBase
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
     * Initiate a refund
     *
     * @param string $orderId
     * @param string $referenceId
     * @param string $amount
     * @param string $remark
     * @return object
     */
    public function create($orderId, $referenceId, $amount, $remark = "")
    {
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }
        $url = $apiEndpoint . "/api/v1/order/refund";

        $this->params['orderId'] = $orderId;
        $this->params['referenceId'] = $referenceId;
        $this->params['refundAmount'] = $amount;
        $this->params['refundNote'] = $remark;

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        if ($response->status == 'ERROR')
            ActivityLogger::Log(2, "Creating Refund Failed: ", (array) $response, __FILE__);

        return $response;
    }

    /**
     * Initiate an instant refund
     *
     * @param string $orderId
     * @param string $referenceId
     * @param string $amount
     * @param string $remark
     * @param string $refundType
     * @param string $merchantRefundId
     * @param string $mode
     * @param string $accountNo
     * @param string $ifsc
     * @return object
     */
    public function instantRefund($orderId, $referenceId, $amount, $remark = "", $refundType = "", $merchantRefundId = "", $mode = "CASHGRAM", $accountNo = "", $ifsc = "")
    {
        $error = 0;
        $error_message = [];
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }
        $url = $apiEndpoint . "/api/v1/order/refund";

        $this->params['orderId'] = $orderId;
        $this->params['referenceId'] = $referenceId;
        $this->params['refundAmount'] = $amount;
        $this->params['refundNote'] = $remark;
        $this->params['mode'] = $mode;

        if ($refundType == 'INSTANT') {
            $this->params['refundType'] = 'INSTANT';
            if ($merchantRefundId == "") {
                $error = 1;
                $error_message[] = "MerchantRefundId cannot be empty. Please read the CashFree PG documentation.";
            } else {
                $this->params['merchantRefundId'] = $merchantRefundId;
            }
        }
        if ($refundType == 'INSTANT' && $mode = 'BANK_TRANSFER') {
            if ($accountNo == "") {
                $error = 1;
                $error_message[] = "Account Number cannot be empty. Please read the CashFree PG documentation.";
            } else {
                $this->params['accountNo'] = $accountNo;
            }
            if ($ifsc == "") {
                $error = 1;
                $error_message[] = "IFSC cannot be empty. Please read the CashFree PG documentation.";
            } else {
                $this->params['ifsc'] = $ifsc;
            }
        }

        if ($error == 1) {
            return (object) $error_message;
        }

        $client = new HttpClient();
        $client->encodeURL(true);
        $response = $client->request('POST', $url, $this->header, $this->params);

        if ($response->status == 'ERROR')
            ActivityLogger::Log(2, "Creating Instant Refund Failed: ", (array) $response, __FILE__);

        return $response;
    }

    /**
     * Get Refund Transactions based on date range
     *
     * @param string $startDate
     * @param string $endDate
     * @param string $lastId Optional
     * @param string $count Optional
     * @return object
     */
    public function retreive($startDate, $endDate, $lastId = "", $count = "")
    {
        if (PaymentGatewayBase::isProduction()) {
            $apiEndpoint = PaymentGatewayBase::getProdURL();
        } else {
            $apiEndpoint = PaymentGatewayBase::getTestURL();
        }
        $url = $apiEndpoint . "/api/v1/refunds";

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
            ActivityLogger::Log(2, "Fetching Refund Transactions Failed: ", (array) $response, __FILE__);

        return $response;
    }
}
