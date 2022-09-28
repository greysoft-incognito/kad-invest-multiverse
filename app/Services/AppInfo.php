<?php

namespace App\Services;

class AppInfo
{
    /**
     * Contruct the the api info
     *
     * @return array
     */
    public static function basic(): array
    {
        return [
            'name' => 'GreyMultiverse',
            'version' => env('APP_VERSION', config('app.api.version.code', '1.0.0')),
            'author' => 'Greysoft',
            'updated' => env('LAST_UPDATE', '2022-06-20 02:27:53'),
        ];
    }

    /**
     * Put the api info into the api collection
     *
     * @return array
     */
    public static function api(): array
    {
        return [
            'api' => self::basic(),
        ];
    }

    /**
     * Append extra data to the api info
     *
     * @return array
     */
    public static function with($data = []): array
    {
        return array_merge(self::api(), $data);
    }
}
