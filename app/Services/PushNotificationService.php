<?php

namespace App\Services;

use App\Traits\Options;
use Illuminate\Support\Facades\Http;

class PushNotificationService {
    use Options;

    static function send($details, $device_id) {
        if(!$device_id) return;
    
        $url = env('FIREBASE_URL');
        $token = $device_id;

        $notification = [
            "to" => $token,
            "notification" => [
                "title" => $details['title'],
                "body" => $details['message'],
            ],
            // "data" => $details['data']
        ];

        $response = Http::withHeaders([
            "Content-Type" => "application/json",
            "Authorization" => "key=".env("FIREBASE_KEY")
        ])->post($url, $notification);
        
        // dd($response->collect());
        return $response;
    }


}
