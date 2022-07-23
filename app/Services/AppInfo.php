<?php

namespace App\Services;

class AppInfo
{
    public static function basic()
    {
        return collect([
            'name' => 'GreyMultiverse',
            'version' => env('APP_VERSION', config('app.api.version.code', '1.0.0')),
            'author' => 'Greysoft',
            'updated' => env('LAST_UPDATE', '2022-06-20 02:27:53'),
        ]);
    }

    public static function api()
    {
        return collect([
            'api' => self::basic(),
        ]);
    }
}