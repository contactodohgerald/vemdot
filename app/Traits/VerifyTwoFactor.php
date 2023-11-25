<?php

namespace App\Traits;
use App\Models\User;
use App\Models\UserCode;
use App\Traits\ReturnTemplate;
use Carbon\Carbon;
use App\Traits\Options;

trait VerifyTwoFactor {
    use ReturnTemplate, Options;

    public function verifyTwofactor($data)
    {
        $user = User::where('unique_id', $data['user_id'])->first();
        if(!$user)
            return['status' => false, 'message' => $this->returnErrorMessage('user_not_found')];
       
        $find = UserCode::where('user_id', $user->unique_id)->where('code', $data['code'])->first();
        if(!$find)
            return ['status' => false, 'message' => $this->returnErrorMessage('wrong_code')];

        if($find->status == 'used' && !in_array(optional($user)->email, $this->emailList))
            return ['status' => false, 'message' => $this->returnErrorMessage('used_code')];

        //add thirty minutes to the time for the code that was created
        $currentTime = Carbon::now()->toDateTimeString();
        $expirationTime = Carbon::parse($find->created_at)->addMinutes(30)->toDateTimeString();
        //compare the dates
        if ($currentTime > $expirationTime && !in_array(optional($user)->email, $this->emailList))
            return ['status' => false, 'message' => $this->returnErrorMessage('expired_token')];
        
        if (!is_null($find)) {
            $user->two_factor_verified_at = Carbon::now()->toDateTimeString();
            $user->save();
            
            $find->status = 'used';
            $find->save();
            
            $find->users;

            return ['status' => true, 'payload' => $find];
        }
        return ['status' => false, 'message' => $this->returnErrorMessage('wrong_code')];
    }
}