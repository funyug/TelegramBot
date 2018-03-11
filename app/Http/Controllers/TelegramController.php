<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    protected $telegram;
    protected $chat_id;
    protected $username;
    protected $text;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    public function getMe()
    {
        $response = $this->telegram->getMe();
        return $response;
    }

    public function setWebHook()
    {
        $url = 'https://inr-bitcoin-bot.herokuapp.com/' . env('TELEGRAM_BOT_TOKEN') . '/webhook';
        $response = $this->telegram->setWebhook(['url' => $url]);

        if ($response == true) {
            return "Webhook setup successfully";
        }

       dd($response);
    }

    public function handleRequest(Request $request)
    {
        $this->chat_id = $request['message']['chat']['id'];
        $this->username = $request['message']['from']['username'];
        $this->text = $request['message']['text'];

        switch ($this->text) {
            case '/start':
            case '/menu':
                $this->showMenu();
                break;
            case '/getTicker':
                $this->getTicker();
                break;
        }
    }

    public function showMenu($info = null)
    {
        $message = '';
        if ($info) {
            $message .= $info . chr(10);
        }
        $message .= '/menu' . chr(10);
        $message .= '/getTicker' . chr(10);

        $this->sendMessage($message);
    }

    public function getTicker()
    {
        $data = $this->getZebpayPrice();

        if($data != "NA") {
            $this->sendMessage($data, true);
        } else {
            $this->sendMessage("Zebpay API is currently down", true);
        }
    }

    protected function sendMessage($message, $parse_html = false)
    {
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $message,
        ];

        if ($parse_html) $data['parse_mode'] = 'HTML';

        $this->telegram->sendMessage($data);
    }

    protected function getZebpayPrice() {
        $client = new Client();
        try {
            $result = $client->get("https://www.zebapi.com/api/v1/market/ticker-new/BTC/INR");
            $result = json_decode($result->getBody());
        } catch (\Exception $e) {
            return "NA";
        }

        $price = $result->market;
        return $price;
    }

    public function removeWebHook() {
        $this->telegram->removeWebhook();
        return "Webhook removed successfully";
    }
}
