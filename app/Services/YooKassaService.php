<?php

namespace App\Services;

use YooKassa\Client;

class YooKassaService
{
    public function client(): Client
    {
        $client = new Client();
        $client->setAuth(
            env('YOOKASSA_SHOP_ID'),
            env('YOOKASSA_SECRET_KEY')
        );

        return $client;
    }
}
