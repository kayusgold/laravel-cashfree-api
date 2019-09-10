<?php

namespace LoveyCom\CashFree\Util;

use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    public static function Log($logType = 1, $title, $body, $file = "")
    {
        if ($file != "")
            $log['file'] = $file;

        $log['info'] = $body;

        // ['id' => $user->id, 'file' => __FILE__, 'line' => __LINE__]

        //$log = "$title ::: $body";
        switch ($logType) {
            case '1':
                Log::info($title, $log);
                break;
            case '2':
                Log::notice($title, $log);
                break;
            case '3':
                Log::warning($title, $log);
                break;
            case '4':
                Log::alert($title, $log);
                break;
            case '5':
                Log::debug($title, $log);
                break;
            case '6':
                Log::emergency($title, $log);
                break;
            case '7':
                Log::error($title, $log);
                break;
            case '8':
                Log::critical($title, $log);
                break;

            default:
                Log::error($title, $log);
                break;
        }
    }

    private static function LogCodes($logType)
    {
        $logs = [
            "1" => "info"
        ];
    }
}
