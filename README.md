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

## Contributing

Thank you for your interest, here are some of the many ways to contribute.

- Check out our [contributing guide](/.github/CONTRIBUTING.md)

## Security

If you discover any security related issues, please email info@plustech.com.ng instead of using the issue tracker.

## License

This software is released under the [MIT](LICENSE) License.


