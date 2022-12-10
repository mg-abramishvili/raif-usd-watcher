<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\TelegramUser;
use App\Models\Rate;
use App\Models\KoronaRate;
use App\Models\UnistreamRate;
use Illuminate\Http\Request;

class RateController extends Controller
{
    public function index()
    {
        return response()->json([
            'raif_rates' => Rate::orderBy('created_at', 'desc')->get(),
            'raif_min' => Rate::orderBy('rate', 'asc')->first(),
            'korona_rates' => KoronaRate::orderBy('created_at', 'desc')->get(),
            'korona_min' => KoronaRate::orderBy('rate', 'asc')->first(),
            'unistream_rates' => UnistreamRate::orderBy('created_at', 'desc')->get(),
            'unistream_min' => UnistreamRate::orderBy('rate', 'asc')->first(),
        ]);
    }

    public function store()
    {
        $this->storeRaif();

        sleep(5);
        $this->storeKorona();
        
        sleep(10);
        $this->storeUnistream();
    }

    public function storeRaif()
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
            $this->sendMessageToTelgeram('Райф', $user, $rate);
        }
    }

    public function storeKorona()
    {
        $url = "https://koronapay.com/transfers/online/api/transfers/tariffs?sendingCountryId=RUS&sendingCurrencyId=810&receivingCountryId=GEO&receivingCurrencyId=840&paymentMethod=debitCard&receivingAmount=150000&receivingMethod=cash&paidNotificationEnabled=false";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // $headers = array(
        //     "Accept: application/json",
        // );
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl); curl_close($curl);

        $responseRate = json_decode($response)[0]->exchangeRate;

        $lastRate = KoronaRate::orderBy('created_at', 'desc')->first();

        if($lastRate && $lastRate->rate == round($responseRate, 2)) {
            return;
        }

        $rate = new KoronaRate();
        $rate->rate = round($responseRate, 2);

        $rate->save();
        
        $telegramUsers = TelegramUser::all();
        
        foreach($telegramUsers as $user)
        {
            $this->sendMessageToTelgeram('Корона', $user, $rate);
        }
    }

    public function storeUnistream()
    {
        $url = "https://online.unistream.ru/card2cash/calculate?destination=GEO&amount=1&currency=USD&accepted_currency=RUB&profile=unistream";

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

        $responseRate = json_decode($response)->fees[0]->acceptedAmount;

        $lastRate = UnistreamRate::orderBy('created_at', 'desc')->first();

        if($lastRate && $lastRate->rate == $responseRate) {
            return;
        }

        $rate = new UnistreamRate();
        $rate->rate = $responseRate;

        $rate->save();
        
        $telegramUsers = TelegramUser::all();
        
        foreach($telegramUsers as $user)
        {
            $this->sendMessageToTelgeram('Юнистрим', $user, $rate);
        }
    }

    public function sendMessageToTelgeram($srv, $user, $rate)
    {
        $url = "https://api.telegram.org/bot";
        $url .= Setting::find(1)->telegram_api_key;
        $url .= "/sendMessage";
        $url .= "?chat_id=";
        $url .= $user->chat_id;
        $url .= "&text=";
        $url .= $srv . ": " . $rate->rate . "; Сумма: $" . round(3492000 / $rate->rate);

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
