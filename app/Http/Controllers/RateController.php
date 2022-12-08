<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\TelegramUser;
use App\Models\Rate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function index()
    {
        return response()->json([
            'rates' => Rate::orderBy('created_at', 'desc')->get(),
            'min' => Rate::orderBy('rate', 'asc')->first(),
        ]);
    }

    public function store()
    {
        $url = "https://www.raiffeisen.ru/oapi/currency_rate/get/?source=RCONNECT&currencies=USD";

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

        $responseRate = json_decode($response)->data->rates[0]->exchange[0]->rates->sell->value;

        $lastRate = Rate::orderBy('created_at', 'desc')->first();

        if($lastRate && $lastRate->rate == $responseRate) {
            return;
        }

        $rate = new Rate();
        $rate->rate = $responseRate;

        $rate->save();
        
        $telegramUsers = TelegramUser::all();
        
        foreach($telegramUsers as $user)
        {
            return $this->sendMessageToTelgeram($user, $rate);
        }
    }

    public function sendMessageToTelgeram($user, $rate)
    {
        $url = "https://api.telegram.org/bot";
        $url .= Setting::find(1)->telegram_api_key;
        $url .= "/sendMessage";
        $url .= "?chat_id=";
        $url .= $user->chat_id;
        $url .= "&text=";
        $url .= "Курс: " . $rate->rate . "; Сумма: $" . round(3492000 / $rate->rate);

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

        $responseJson = json_decode($response);
    }
}
