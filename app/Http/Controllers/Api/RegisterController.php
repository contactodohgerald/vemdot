<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role\AccountRole;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;
use App\Models\Verification\Verification;

class RegisterController extends Controller
{
    //
    function __construct(Verification $verification, User $user){
        $this->verification = $verification;
        $this->user = $user;
    }

    public function register(Request $request){
        $data = $request->all();

        // return Response::json(['data' => $data]);
        $validator = Validator::make($data, [
            'name' => 'required|between:2,100',
            'phone' => 'required|unique:users',
            'role' => 'required|exists:account_roles,unique_id',
            'email' => 'required|email|unique:users|max:50',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'delivery_fee' => 'nullable|numeric|min:0',
        ]);

        $appSettings = $this->getSiteSettings();

        if($validator->fails())
            return $this->returnMessageTemplate(false, $validator->errors());

        if($data['referred_id'] != ''){
            $users = $this->user->getUser([
                ['referral_id', $data['referred_id']]
            ]);

            if($users == null){
                return $this->returnMessageTemplate(false, $this->returnErrorMessage('refferral_not_found'));
            }else{
                $users->main_balance += $appSettings->referral_bonus;
                $users->save();
            }
        }

        $role = AccountRole::find($request->input('role'));
        $deliveryFee = $request->input('delivery_fee') ?: $appSettings->delivery_fee;


        $user = User::create([
            'unique_id' =>  $this->createUniqueId('users'),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'country' => $data['country'] ?? null,
            'gender' => $data['gender'] ?? null,
            'referral_id' => $this->createUniqueId('users', 'referral_id'),
            'referred_id' => $data['referred_id'] ?? null,
            'password' => Hash::make($data['password']),
            'delivery_fee' => $role->name == 'Logistic' ? $deliveryFee : null
        ]);

        event(new Registered($user));

        if($appSettings->welcome_message != 'no') //send welcome message to newly registerd user
            $this->verification->sendWelcomeMail($user, $appSettings);

        $payload = ['user' => $user];

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('account_registered'), $payload);
    }
}
