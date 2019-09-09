<?php

namespace LoveyCom\CashFree\HttpClient;

use Illuminate\Support\Facades\Log;

class HttpClient
{
    protected $timeout = 60;
    protected $encodeURL = false;

    public function encodeURL($status = false)
    {
        if ($status == true) {
            $this->encodeURL = true;
        }
    }

    public function request($method = 'GET', $url, $header = array(), $params = array())
    {
        $header[] = 'Cache-Control: no-cache';
        $header[] = 'Content-Type: application/json';

        //Log::info(json_encode($header));

        $ch = curl_init();
        $payload = json_encode($params);

        if ($method == 'POST' && $this->encodeURL == true) {
            $request_string = "";
            foreach ($params as $key => $value) {
                $request_string .= $key . '=' . rawurlencode($value) . '&';
            }
            curl_setopt($ch, CURLOPT_POST, count($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request_string);
        } else if ($method == 'POST' && $this->encodeURL == false) {
            curl_setopt($ch, CURLOPT_POST, count($params));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            $header['Content-Length'] = strlen($payload);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, "$url?");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        $curl_result = curl_exec($ch);

        if (curl_error($ch))
            Log::error("CURL Error: " . curl_error($ch));

        curl_close($ch);

        return json_decode($curl_result);
    }
}
