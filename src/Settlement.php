<?php

namespace LoveyCom\CashFree;

use LoveyCom\CashFree\HttpClient\HttpClient;
use LoveyCom\CashFree\Util\ActivityLogger;

/**
 * This class enables us to check the settlement status of orders
 *  Status                  | Meaning
 * -------------------------|------------------------
 *  PG_UNPAID               | The order is still not paid. Unpaid orders can be settled to vendors
 *  PG_SETTLEMENT_PENDING   | The order is still not settled from PG to marketplace. Settlement to vendors only happens after settlement at PG.
 *  READY_FOR_SETTLEMENT    | The Order is ready for settlement and will be settled within one working day typically.
 *  INITIATED_SETTLEMENT    | The Settlement to vendors has been initiated but not all vendors have been settled. Check vendorTransfers in response for more details.
 *  SETTLED                 | The Settlemend to all vendors has been completed. Check vendorTransfers in response for more details.
 */
class Settlement
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
     * Get a SETTLEMENT STATUS of an ORDER
     *
     * @param string $vendorId
     * @return object
     */
    public function status($orderId)
    {
        if (CashFree::getIsProduction()) {
            $url = Config::get('cashfree.prodURL', 'https://ces-api.cashfree.com') . "/ces/v1/getOrderSettlementStatus/$orderId";
        } else {
            $url = Config::get('cashfree.testURL', 'https://ces-gamma.cashfree.com') . "/ces/v1/getOrderSettlementStatus/$orderId";
        }

        $client = new HttpClient();

        $response = $client->request('GET', $url, $this->header);

        if ($response->status != "SUCCESS") {
            ActivityLogger::Log(2, "Get Settlement Status Request Failed: ", (array) $response, __FILE__);
        }

        return $response;
    }
}
