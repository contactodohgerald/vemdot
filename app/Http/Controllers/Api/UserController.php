<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\UpdateUserRequest;
use App\Models\Role\AccountRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller{

    public function completeProfileSetup(UpdateUserRequest $request){
        $user = $this->user();
        
        $validate = Validator::make($request->all(), [
            'id_number' => 'required|string',
            'id_image' => 'required|string'
        ]);
        
        if($validate->fails()) return $this->returnMessageTemplate(false, '', $validate->errors());
        

        $user->update($request->safe()->merge([
            'id_number' => $request->id_number,
            'id_image' => $request->id_image,
            'kyc_status' => 'pending',
            'push_notification'=> $request->push_notification ?? $user->push_notification,
            'geolocation' => $request->geolocation
        ])->all());

        return $this->returnMessageTemplate(true,
                        "Profile Updated Sucessfully!", [
                            'user' => $user
                        ]);
    }

    public function updateDeviceId(Request $request){

        if(!$request->device_id) return $this->returnMessageTemplate(false, 'Device Id Required');
        $user = $this->user();
        $user->device_id = $request->device_id;
        $user->save();

        return $this->returnMessageTemplate(true, "", ['user' => $user]);

    }

    public function update(UpdateUserRequest $request){
        $user = $this->user();

        // check unique email except this user
        if(isset($request->email)){
            $check = User::where('email', $request->email)
                     ->where('unique_id', '!=', $user->unique_id)
                     ->count();
            if($check > 0){
                return $this->returnMessageTemplate(false, "This Email Address has already been used");
            }
        }

        $user->update($request->safe()->merge([
                'push_notification'=> $request->push_notification ?? $user->push_notification
        ])->all());
        
        return $this->returnMessageTemplate(true, "Profile updated Successfully", $user);
    }

    public function show(){
        $user = $this->user();
        $user->notifications;
        $user->userRole;

        return $this->returnMessageTemplate(true, "", [
            'user' => $user,
        ]);
    }

    public function list($role){
        $query = User::where('role', $role);
        $role = AccountRole::find($role);

        if(!$role) return $this->returnMessageTemplate(false, "The specified user type does not exist!");

        if($role->name === 'Vendor'){
            $query->with(['meals']);
        }

        if($role->name === 'User'){
            $query->with(['addresses']);
        }

        if($role->name === 'Logistic'){
            $query->with(['bikes']);
        }

        if($role->name === 'Rider'){
            $query->with(['bikeOwner']);
        }

        // $query->notifications;

        return $this->returnMessageTemplate(true, "", [
            'users' => $query->get()
        ]);
    }

    public function fetchUserByEmail(Request $request){
        if($request->isNotFilled('email')) return $this->returnMessageTemplate(false, 'Please provide a valid email address!');

        $user = User::where('email', $request->email)->first();
        if(!$user) return $this->returnMessageTemplate(false, "A user with email '$request->email' does not exist!");

        return $this->returnMessageTemplate(true, "", [
            'user' => $user
        ]);

    }

    public function single($role, $user_id){
        $user = User::findOrFail($user_id);
        $query = User::query();
        $query->with('wallet');

        if($user->userRole->name === $role) return $this->returnMessageTemplate(false, "User is not authorized to take this action");

        if($role === 'Vendor'){
            $query->with(['meals']);
        }

        if($role === 'User'){

        }

        if($role === 'Logistic'){

        }

        $query->first();

    

        return $this->returnMessageTemplate(true, "", [
            'user' => $user
        ]);

    }

    function allUsers(){
        return $this->returnMessageTemplate(true, "", [
            'user' => User::with('userRole')->get()
        ]);
    }

    function fetchAccountRoles(){
        $roles = AccountRole::where('name', '!=', 'Super Admin')->where('name', '!=', 'Admin')->get();
        return $this->returnMessageTemplate(true, '', $roles);
    }

    function fetchCurrentUserRole(){
        $user = $this->user();
        $role = $user->userRole;
        return $this->returnMessageTemplate(true, '', $role);
    }
}
