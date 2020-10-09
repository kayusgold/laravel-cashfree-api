# CashFree Payment Gateway Laravel Package

[![Issues](https://img.shields.io/github/issues/kayusgold/laravel-cashfree-api?style=flat-square)](https://github.com/)
[![Stars](https://img.shields.io/github/stars/kayusgold/laravel-cashfree-api?style=flat-square)](https://github.com/)
[![Forks](https://img.shields.io/github/forks/kayusgold/laravel-cashfree-api?style=flat-square)](https://github.com/)
[![License](https://img.shields.io/github/license/kayusgold/laravel-cashfree-api?style=flat-square)](https://github.com/)



An open source package by [kayusgold](https://plustech.com.ng) for [CashFree](https://cashfree.com), an Indian payment gateway.

## Documentation

CashFree offers their clients many services to make transactions between sender and receiver seemlessly easy. However, this package focuses mainly on MarketPlace Settlement API and Payment Gateway API. Visit [here](http://docs.cashfree.com/docs/ces/guide/) for MarketPlace Settlement API documentation and [here](https://docs.cashfree.com/docs/rest/guide/) for Payment Gateway API documentation.

Using the package, but you're stuck? Found a bug? Have a question or suggestion for improving this package? Feel free to create an issue on GitHub, we'll try to address it as soon as possible.

## Requirements 

1. PHP >= 7.0.*
2. Laravel >= 5.6.*

## Installation

1. To use LoveyCom CashFree package, add it to your project using composer:
```
composer require loveycom/cashfree
```

2. Open Config/app.php and add the following to the providers array:
```php
LoveyCom\CashFree\CashFreeServiceProvider::class,
```

3. Run the command below to publish the package config file config/cashfree.php:
```php
php artisan vendor:publish
```

4. Open config/cashfree.php to edit neccessary parameters such as API Key ID and Secret Key.
```json
    'appID' => '',
    'secretKey' => '',
    'testURL' => 'https://ces-gamma.cashfree.com',
    'prodURL' => 'https://ces-api.cashfree.com',
    'maxReturn' => 100,
    'isLive' => false,
```

Do not forget to dump composer autoload
```
composer dump-autoload
```

## USAGE
1. Edit the cashfree.php already published to your app config. Location: ```config/cashfree.php```
 - Fill all the required details
2. The cashfree api wrapper follows the cashfree payment gateway categories (Marketplace & PaymentGateway) - read more from their docummentation
3. Under the Marketplace category, this wrapper provides you with the following classes and methods
 - ### Marketplace
  * ```checkBalance()``` -- endpoint /getBalance
  * ```withdraw($amount, $remark = "")``` -- endpoint /ces/v1/requestWithdrawal
  * ```getLedger($maxReturn = "", $lastReturnId = "")``` -- endpoint /ces/v1/getLedger?maxReturn=$maxReturn
 
 - ### Settlement
  * ```status($orderId)``` -- endpoint /ces/v1/getOrderSettlementStatus/$orderId
 
 - ### Transaction
  * ```importTransaction($details = [])``` -- endpoint /ces/v1/importTransaction
  * ```retreive($orderId = "")``` -- endpoint /ces/v1/getTransactions or /ces/v1/getTransaction/$orderId
  * ```attachVendorToTransaction($orderId, $vendorId, $commission = "", $commissionAmount = "")``` -- endpoint /ces/v1/attachVendor
  * ```detachVendorFromTransaction($orderId, $vendorId)``` -- endpoint /ces/v1/detachVendor
  
 - ### Vendor
  * ```create($vendor = [], $vendorId = "")``` -- endpoints  /ces/v1/editVendor/$vendorId (Update Vendor Details) | /ces/v1/addVendor (Create Vendor)
  * ```retreive($vendorId = "")``` -- endpoints -- endpoints /ces/v1/getVendor/$vendorId | /ces/v1/getVendors
  * ```checkStatus($vendorId)``` -- endpoint /ces/v1/getVendor/$vendorId
  * ```adjustVendorBalance($vendorId, $adjustmentId, $amount, $type = "CREDIT", $remark = "")``` -- endpoint /ces/v1/adjustVendor
  * ```requestVendorPayout($vendorId, $amount)``` -- endpoint /ces/v1/requestVendorPayout
  * ```getLedger($vendorId, $maxReturn = 50, $lastReturnId = "")``` -- endpoint /ces/v1/getVendorLedger/$vendorId?maxReturn=$maxReturn
  * ```getTransferDetails($vendorTransferId = "", $vendorId = "", $maxReturn = 50, $lastReturnId = "", $startDate = "", $endDate = "")``` -- endpoint /ces/v1/getVendorTransfer/...
  * ```transferBetweenVendors($fromVendorId, $toVendorId, $amount, $adjustmentId)``` -- endpoint /transferVendorBalance
 
4. The PaymentGateway has the following classes and methods
 - ### Order
  * ```create($order)``` -- endpoint /api/v1/order/create
  * ```getLink($orderId)``` -- endpoint /api/v1/order/info/link
  * ```getDetails($orderId)``` -- endpoint /api/v1/order/info/
  * ```getStatus($orderId)``` -- endpoint /api/v1/order/info/status
 
 - ### Refund
  * ```create($orderId, $referenceId, $amount, $remark = "")``` -- endpoint /api/v1/order/refund
  * ```instantRefund($orderId, $referenceId, $amount, $remark = "", $refundType = "", $merchantRefundId = "", $mode = "CASHGRAM", $accountNo = "", $ifsc = "")```  -- endpoint /api/v1/order/refund
  * ```retreive($startDate, $endDate, $lastId = "", $count = "")``` -- endpoint /api/v1/refunds
  
 - ### Settlement
  * ```getAll($startDate, $endDate, $lastId = "", $count = "")``` -- endpoint /api/v1/settlements
  * ```getOne($settlementId)``` -- endpoint /api/v1/settlement
  
 - ### Transaction
  * ```retreive($startDate = "", $endDate = "", $txStatus = "", $lastID = "", $count = "")``` -- endpoint /api/v1/transactions
  
5. ### USAGE EXAMPLE
 - To use the Order class of the PaymentGateway Category,
	```php
    //import the class
    use LoveyCom\CashFree\PaymentGateway\Order;
    
    //instantiate the class
	$order = new Order();
	//prepare the order details
	//NOTE: Prepare a route for returnUrl and notifyUrl (something like a webhook). However, if you have webhook setup in your cashfree dashboard, no need for notifyUrl. But if notifyUrl is set, it will be called instead.
	$od["orderId"] = "ORDER-84984941";
    $od["orderAmount"] = 10000;
    $od["orderNote"] = "Subscription";
    $od["customerPhone"] = "9000012345";
    $od["customerName"] = "Test Name";
    $od["customerEmail"] = "test@cashfree.com";
    $od["returnUrl"] = "http://127.0.0.1:8000/order/success";
    $od["notifyUrl"] = "http://127.0.0.1:8000/order/success";
	//call the create method
	$order->create($od);
	//get the payment link of this order for your customer
	$link = $order->getLink($od['orderId'])
	//You can now either send this link to your customer through email or redirect to it for them to complete the payment.
	//To confirm the payment,
	//Call either getDetails($orderId) or getStatus($orderId) method
    ```

## Contributing

Thank you for your interest, here are some of the many ways to contribute.

- Check out our [contributing guide](/.github/CONTRIBUTING.md)

## Security

If you discover any security related issues, please email info@plustech.com.ng instead of using the issue tracker.

## License

This software is released under the [MIT](LICENSE) License.


