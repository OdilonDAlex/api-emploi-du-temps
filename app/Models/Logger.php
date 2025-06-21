<?php


namespace App\Models;

use Carbon\Carbon;

class Logger
{
    private static string $filepath = 'log.txt';

    public static function log(string $message, string $type = 'LOG')
    {
        $now = Carbon::now();
        $file = fopen(Logger::$filepath, 'a+');
        fwrite($file, $now->format('H-i-s') . " " . $type . " " . $message . PHP_EOL);
        fclose($file);
    }
}
