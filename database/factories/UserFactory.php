<?php

namespace Database\Factories;

use App\Models\Role\AccountRole;
use App\Traits\Generics;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactorys extends Factory {
    use Generics;

    protected $model = User::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $roles = AccountRole::whereIn('name', ["Vendor", "Logistic", "Rider", "User"])->get();

        return [
            'unique_id' => $this->createUniqueId('users'),
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'kyc_status' => 'confirmed',
            'status' => 'confirmed',
            'role' => $roles->random()->unique_id,
            'email_verified_at' => now(),
            'password' => Hash::make('test@2022'), // password
            'remember_token' => Str::random(10),
        ];
    }
}

