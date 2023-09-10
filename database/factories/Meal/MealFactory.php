<?php

namespace Database\Factories\Meal;

use App\Models\Meal\Meal;
use App\Models\Meal\MealCategory;
use App\Models\User;
use App\Traits\Generics;
use Illuminate\Database\Eloquent\Factories\Factory;

class MealFactory extends Factory {
    use Generics;

    protected $model = Meal::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(4),
            'thumbnail' => $this->faker->imageUrl(),
            'description' => $this->faker->text(100),
            'price' => $this->faker->randomNumber(4),
            'images' => [$this->faker->imageUrl(), $this->faker->imageUrl()],
            'video' => $this->faker->imageUrl(),
            'discount' => $this->faker->numberBetween(0, 100),
            'tax' => $this->faker->numberBetween(0, 100),
            'category' => MealCategory::all()->collect()->random(1),
            'availability' => $this->faker->boolean(70),
            'avg_time' => $this->faker->randomNumber(2),
            'unique_id' => $this->createUniqueId('meals'),
            'user_id' => User::roleVendor()->get()->random()->unique_id
        ];
    }


}
