<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Traits\ReturnTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UpdatePasswordContoller extends Controller
{
    use ReturnTemplate;
    //
    function __construct( User $user){
        $this->user = $user;
    }

    public function updateUserPassword(Request $request){
        $data = $request->all();

        $validator = Validator::make($data, [
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        if($validator->fails()){
            return $this->returnMessageTemplate(false, $validator->errors());
        }

        $user = $this->user();
        if($user == null)
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('user_not_found'));

        if(! Hash::check($data['current_password'], $user->password))
            return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_equal_password'));

        $user->password = Hash::make($data['password']);
        if($user->save()){
            return $this->returnMessageTemplate(true, $this->returnSuccessMessage('successful_updated'));
        }
    }
}
