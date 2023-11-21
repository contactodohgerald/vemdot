<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InactiveUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DeleteAccountController extends Controller
{

    public function deleteAccount(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'email' => 'required|email',
            'password' => 'required',
            'reason' => 'required',
        ]);
        
        if($validator->fails()) return $this->returnMessageTemplate(false, $validator->errors());
        

        $user = User::where('email', $input['email'])->first();
        if (!$user) return $this->returnMessageTemplate(false, $this->returnErrorMessage('user_not_found'));

        if(! Hash::check($input['password'], $user->password)) return $this->returnMessageTemplate(false, $this->returnErrorMessage('wrong_password'));

        $inactiveUserData = [
            'unique_id' => $this->createUniqueId('inactive_users'),
            'name' => $user->name,
            'email' => $user->email,
            'country' => $user->country,
            'phone' => $user->phone,
            'gender' => $user->gender,
            'wallet_balance' => $user->wallet_balance,
            'address' => $user->address,
            'reason' => $input['reason']
        ];
    
        $inactiveUser = InactiveUser::create($inactiveUserData);

        if ($inactiveUser) {
            $user->delete();
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('deleted_account'), $inactiveUser);
        } else {
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('unknown_error'));
        }
    }
    
}
