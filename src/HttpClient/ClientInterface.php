<?php

namespace LoveyCom\CashFree\HttpClient;

interface ClientInterface
{
    public function request($method, $url, $headers, $params);
}
