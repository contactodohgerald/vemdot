<?php

namespace App\Console\Commands;

use App\Models\Meal\Meal;
use App\Models\Meal\MealCategory;
use App\Models\Role\AccountRole;
use App\Models\User;
use App\Traits\Generics;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedDummyMeals extends Command {
    use Generics;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:meals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $roles = AccountRole::whereIn('name', ["Vendor", "Logistic", "Rider", "User"])->get();
        $faker = \Faker\Factory::create();
        
        $city = ['Uyo', 'Enugu', 'Abuja', 'Ikeja'];
        $state = ['Akwa Ibom', 'Enugu', 'Abuja', 'Lagos'];

        $data = [];
        for ($i=0; $i < 20; $i++) { 
            $data[] = [
                'unique_id' => $this->createUniqueId('users'),
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kyc_status' => 'confirmed',
                'status' => 'confirmed',
                'phone' => $faker->e164PhoneNumber,
                'role' => $roles->random()->unique_id,
                'email_verified_at' => now(),
                'password' => Hash::make('test@2022'), // password
                'remember_token' => Str::random(10),
                'availability' => 'yes',
                'two_factor' => 'no',
                'two_factor_access' => 'text',
                'main_balance' => 0,
                'ref_balance' => 0,
                'city' => $city[rand(0, 3)],
                'state' => $state[rand(0, 3)],
                'first_time_login' => 'yes',
                'created_at' => now(),
                'updated_at' => now(),
                'referral_id' => $this->createUniqueId('users', 'referral_id')
            ];
        }

        User::insert($data);

        $meals = [];
        $status = ['yes', 'no'];

        $images = ['https://res.cloudinary.com/dzfy28xb9/image/upload/v1657883962/vemdot/image/civabibrb3xhtv4wjiqp.jpg', 'https://res.cloudinary.com/dzfy28xb9/image/upload/v1657883967/vemdot/image/ushqx7ueihlgavqphjal.jpg'];

        for ($i=0; $i < 50; $i++) { 
            $meals[] = [
                'name' => $faker->sentence(4),
                'thumbnail' => $images[0],
                'description' => $faker->text(100),
                'price' => $faker->randomNumber(4),
                'images' => json_encode($images),
                'video' => "https://youtu.be/k7Gee7kNF3E",
                'discount' => $faker->numberBetween(0, 100),
                'tax' => $faker->numberBetween(0, 100),
                'category' => MealCategory::all()->collect()->random(1)[0]->unique_id,
                'avg_time' => $faker->randomNumber(2),
                'unique_id' => $this->createUniqueId('meals'),
                'user_id' => User::roleVendor()->get()[rand(0, 3)]->unique_id,
                'availability' => $status[rand(0,1)],
                'rating' => rand(1,5),
                'promoted' => 'no',
                'created_at' => now(),
                'updated_at' => now(),
                'total_orders' => rand(5, 30)
            ];
        }
        Meal::insert($meals);
    }
}
