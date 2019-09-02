<?php

namespace LoveyCom\CashFree;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class CashFreeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/cashfree.php', 'cashfree');
        //php artisan vendor:publish
        $this->publishes([
            __DIR__ . '/../config/cashfree.php' => config_path('cashfree.php'),
        ]);
    }
    public function register()
    { }
}
