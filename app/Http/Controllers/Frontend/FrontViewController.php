<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class FrontViewController extends Controller
{
    //
    public function getFrontView(){
        $appSettings = $this->getSiteSettings();

        $payload = [
            'appSettings' => $appSettings,
        ];
        return view('frontend.index', $payload);
    }
}
