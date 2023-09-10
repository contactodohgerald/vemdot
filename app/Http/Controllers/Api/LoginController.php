<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Verification\Verification;
use App\Services\SendTextMessage;
use Carbon\Carbon;

class LoginController extends Controller
{
    public $verification;
    public $user;

    function __construct(Verification $verification, User $user){
        $this->verification = $verification;
        $this->user = $user;
    }
    //

    public function loginUser(SendTextMessage $sendTextMessage, Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->errors());
       
        if (!Auth::attempt($request->only('email', 'password'))) return $this->returnMessageTemplate(false, $this->returnErrorMessage('wrong_crendential'));

        $user = User::where('email', $request['email'])->firstOrFail();

        $appSettings = $this->getSiteSettings();

        //check if the user is blocked
        if($user->status == $this->blocked){
            $this->logoutUser();
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('account_blocked'));
        }

        $user->two_factor_verified_at = null;
        $user->save();

        $data = $user->generateCodeFor2fa($user);
        $notification = $this->notification();
        // if($user->two_factor_access == 'email'){
           //send mail incase of an error
        $notification->subject('Your confirmation code')
            ->text('Verification Needed')
            ->text('Please confirm your sign-in request')
            ->text('We have detected an account sign-in request from a device we don`t recognize')
            ->code($data['code'])
            ->text('To verify your account, please use the following code to sign-in to your account')
            ->send($user, ['mail']);
        // }
        $message =  $appSettings->site_name.": ".$data['code']." is your security code. It exipres in 15 minutes. Don't share this code with anyone";
        //send text message to the user
        // $sendTextMessage->sendTextMessage($user->phone, $appSettings->site_name, $message);
  
        if(env('APP_ENV') == 'local'){
            $payload = [
                '2fa_code' => $data['code'],
                'user' => $user->with('userRole')->find($user->unique_id),
            ];
        }else{
            $payload = [
                'user' => $user->with('userRole')->find($user->unique_id),
            ];
        }

        if($data['status']){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('2fa_code_sent'), $payload);
        }else{
            return $this->returnMessageTemplate(false, $data['message']);
        }
    }

    public function processUserlogin(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'user_id' => 'required',
            'code' => 'required',
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->errors());

        $process = $this->verifyTwofactor($data);

        if($process['status']){

            $appSettings = $this->getSiteSettings();

            $user = $process['payload']->users;
            $currentDate = Carbon::now();

            //check for unactivated account
            if($user->email_verified_at == null){
                //verify user account
                $user->update([
                    'email_verified_at' => $currentDate->toDateTimeString(),
                    'two_factor' => 'yes',
                    'status' => $this->confirmed,
                ]);           
            }

            if($appSettings->login_alert != 'no'){
                $dateFormat = $currentDate->format('l jS \\of F Y h:i:s A');
                //send login notifier to users
                $this->verification->procastLoginMailToUser($user, $dateFormat, $appSettings);
            }

            $token = $user->createToken('auth_token', ['full_access'])->plainTextToken;

            $payload = [
                'token' => $token,
                'user' => $user,
            ];
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_login'), $payload);

        }else{
            return $this->returnMessageTemplate(false, $process['message']);
        }
    }
    
}
