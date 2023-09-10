<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Verification\Verification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class ResetPasswordContoller extends Controller
{
    //
    function __construct(User $user, Verification $verification){
        $this->user = $user;
        $this->verification = $verification;
    }

    //function that send's code to user mail for password reset
    public function passwordResetCode(Request $request) {
        $data = $request->all();

        $validator = Validator::make($data, [
            'email' => 'required',
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->errors());

        //get the user object
        $user = $this->user->getUser([
            ['email', $data['email']],
        ]);

        if($user == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('user_not_found'));
        

        $appSettings = $this->getSiteSettings();

        //send the user an email for activation of account and redirect the user to the page where they will enter code
        $verificationCode = $this->verification->createActivationCode($user, $appSettings, "password-reset");
        if($verificationCode['status']){
            //send the activation code via email to the user
            $this->verification->sendPwdResetTokenMail($verificationCode['token'], $user, $appSettings);
            //return the account activation code and email
            $payload = [
                'user' => $user,  
                'token' => $verificationCode['token']
            ];
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('activation_token_sent'), $payload);
        }
    }

    public function resendResetCode(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'userId' => 'required',
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->errors());
        

        //get the user object
        $user = $this->user->getUser([
            ['unique_id', $data['userId']],
        ]);

        if($user == null){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('user_not_found'));
        }

        $appSettings = $this->getSiteSettings();

        //send the user an email for activation of account and redirect the user to the page where they will enter code
        $verificationCode = $this->verification->createActivationCode($user, $appSettings, "password-reset");
        if($verificationCode['status']){
            //send the activation code via email to the user
            $this->verification->sendPwdResetTokenMail($verificationCode['token'], $user, $appSettings);
            //return the account activation code and email
            $payload = [
                'user' => $user,  
                'token' => $verificationCode['token']
            ];
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('activation_token_sent'), $payload);
        }
    }

    public function verifySentResetCode(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'userId' => 'required',
            'code' => 'required',
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->errors());

        //get the user object
        $user = $this->user->getUser([
            ['unique_id', $data['userId']],
        ]);

        if($user == null){
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('user_not_found'));
        }

        //verify the token
        $verificationCode = $this->verification->verifyTokenValidity($data['code'], 'password-reset', $user);
        if(!$verificationCode['status']){
            return $this->returnMessageTemplate(false, $verificationCode['message']);
        }
    
        $payload = ['user' => $user];
        return $this->returnMessageTemplate(true, $verificationCode['message'], $payload);    
    }

    public function resetPassword(Request $request){
        $data = $request->all();

        $appSettings = $this->getSiteSettings();

        $validator = Validator::make($data, [
            'userId' => 'required',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->errors());

        //get the user object
        $user = $this->user->getUser([
            ['unique_id', $data['userId']],
        ]);
        if($user == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('user_not_found'));

        $user->password = Hash::make($data['password']);
        if($user->save()){
            if($appSettings->send_basic_emails != 'no'){
                $currentDate = Carbon::now();
                $dateFormat = $currentDate->format('l jS \\of F Y h:i:s A'); 
                //send reset password mail to user
                $this->verification->sendUserPasswordResetMail($user, $dateFormat, $appSettings);
            }

            //password reset was successful, login to continue
            $payload = ['user' => $user];
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('password_reset'), $payload);
        }    
    }
}
