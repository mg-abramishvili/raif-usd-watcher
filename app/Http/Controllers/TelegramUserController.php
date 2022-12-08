<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\TelegramUser;
use Illuminate\Http\Request;

class TelegramUserController extends Controller
{
    public function check()
    {
        $url = "https://api.telegram.org/bot";
        $url .= Setting::find(1)->telegram_api_key;
        $url .= "/getupdates";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Accept: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl); curl_close($curl);

        return $response;
    }
}
