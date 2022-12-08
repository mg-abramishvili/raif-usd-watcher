<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\TelegramUser;
use Illuminate\Http\Request;

class TelegramUserController extends Controller
{
    public function index()
    {
        return TelegramUser::all();
    }

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

        $responseJson = json_decode($response);

        foreach($responseJson->result as $resultItem)
        {
            $telegramUser = TelegramUser::where('chat_id', $resultItem->message->chat->id)->first();

            if($telegramUser) {
                return;
            }

            $telegramNewUser = new TelegramUser();
            $telegramNewUser->first_name = $resultItem->first_name;
            $telegramNewUser->last_name = $resultItem->last_name;
            $telegramNewUser->username = $resultItem->username;
            $telegramNewUser->chat_id = $resultItem->chat_id;

            $telegramNewUser->save();
        }
    }
}
