<?php

namespace App\Http\Requests\Api\Users;

use App\Models\User;
use App\Traits\ReturnTemplate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest{
    use ReturnTemplate;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException($this->returnMessageTemplate(false, '', $validator->errors()));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        // $user = User::find($this->user()->unique_id);
        $user = auth()->user();
        // Rule::requiredIf($user->isLogistic() || $user->isVendor())
        // Rule::requiredIf($user->isLogistic() || $user->isVendor()),
        return [
            'avatar' => 'nullable|string|url',
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->unique_id, 'unique_id')],
            'id_number' => ['nullable', 'string'],
            'id_image' => ['nullable', 'url'],
            'logo' => 'nullable|string|url',
            'business_name' => ['nullable', 'string'],
            'city' => ['string'],
            'state' => [ 'string'],
            'address' => ['string'],
            'avg_time' => ['nullable', 'string'],
            'password' => 'nullable|confirmed',
            'old_password' => 'required_with:password',
            'coordinates' => 'nullable|array',
            'delivery_fee' => 'nullable|numeric|min:0',
            'push_notification' => 'nullable|in:yes,no',
            'availability' => 'nullable|in:yes,no',
            'geolocation' => 'nullable|array'
        ];
    }
}
