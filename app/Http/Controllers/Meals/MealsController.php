<?php

namespace App\Http\Controllers\Meals;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Meals\CreateMealRequest;
use App\Models\Meal\Meal;
use App\Models\Meal\MealCategory;
use App\Models\Order;
use App\Models\User;
use App\Services\MealService;
use Illuminate\Http\Request;

class MealsController extends Controller{

    public $mealService;

    function __construct(MealService $mealService){
        $this->mealService = $mealService;
    }

    function create(CreateMealRequest $request){
        $user = $this->user();

        if(!MealCategory::find($request->category)) return $this->returnMessageTemplate(false, "The Selected Meal Category Does not Exist");

        $unique_id = $this->createUniqueId('meals');

        $meal = Meal::create($request->safe()->merge([
            'unique_id' => $unique_id,
            'user_id' => $user->unique_id,
            'images' => $request->images
        ])->all());

        $meal = Meal::where('unique_id', $unique_id)->with('categories')->first();

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('created', 'Meal'), $meal);
    }

    function update(CreateMealRequest $request, $meal_id){
        $user = $this->user();
        if(!$meal = Meal::findOrFail($meal_id)) return $this->returnMessageTemplate(false, "Meal does not exist");
        if(!$meal->user_id && $user->unique_id) return $this->returnMessageTemplate(false, "The Meal does not belong to this user");

        $images = $meal->images;

        if($request->filled('images')) $images = $request->images;

        if(!MealCategory::find($request->category)) return $this->returnMessageTemplate(false, "The Selected Meal Category Does not Exist");

        $meal->update($request->safe()->merge([
            'images' => $images
        ])->all());

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('updated', "Meal"), $meal->with('categories')->first());
    }

    function vendorMeals(Request $request, MealService $mealService, $vendor_id = null){
        $user = $this->user();

        $meals = $mealService->category()
                            ->filterByCategory()
                            ->status()->orderBy()
                            ->search()->orders("withCount")
                            ->owner();

        if($user && $user->userRole->name === 'Vendor') {
            $meals->byUser($user->unique_id);
        }else if($vendor_id){
            $meals->byUser($vendor_id);
        }else{
            return $this->returnMessageTemplate(false, 'Invalid Request. Vendor not provided');
        }

        $meals = $meals->query();
        
        $meals->when($request->input('min_time'), function($query, $time) {
            $query->where('avg_time', '>=', $time);
        });
        
        $meals->when($request->input('max_time'), function($query, $time) {
            $query->where('avg_time', '<=', $time);
        });

        $meals = $meals->withExists([
            'favourites as is_favourite' => function($query) use($user){
                return $query->where('user_id', $user->unique_id);
            }])->paginate($this->paginate);

        foreach ($meals as $meal) {
            $orders = Order::whereJsonContains('meals', ['meal_id' => $meal->unique_id])->get();
            $meal->orders = $orders;
            $meal->order_count = $orders->count();
        }

        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', "Meal"), $meals);
    }


    function delete(Request $request, MealService $mealService, $meal_id){
        $mealService->find($meal_id)->delete();
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('deleted', "Meal"));
    }

    public function fetchAllMeals2(Request $request, MealService $mealService, $vendor_id = null)
    {
        $user = $this->user();

        return $user;

        $meals = $mealService
            ->byUser($vendor_id)
            ->hasVendor()
            ->owner()
            ->category()
            ->orderBy()
            ->orders(true)
            ->search()
            ->filterByCity()
            ->filterByCategory()
            ->status()
            ->filterByGeolocation()
            ->query();

        $meals->when($request->input('min_time'), function ($query, $time) {
            $query->where('avg_time', '>=', $time);
        });

        $meals->when($request->input('max_time'), function ($query, $time) {
            $query->where('avg_time', '<=', $time);
        });

        if (request()->input('geolocation')) {
            $meals = $meals->paginate($this->paginate);
        } else {
            $meals->withExists([
                'favourites as is_favourite' => function ($query) use ($user) {
                    $query->where('user_id', $user->unique_id);
                }
            ]);
            $meals = $meals->paginate($this->paginate);
        }

        return $this->returnMessageTemplate(true, '', $meals);
    }

    public function fetchAllMeals(Request $request, MealService $mealService, $vendor_id = null){
        $user = $this->user();

        $meals = $mealService
            ->byUser($vendor_id)
            ->filterByGeolocation()
            ->category()
            ->orderBy()
            ->orders(true)
            ->search()
            ->filterByCity()
            ->status()
            ->filterByCategory()
            ->query();

        $meals->when($request->input('min_time'), function($query, $time) {
            $query->where('avg_time', '>=', $time);
        });
        
        $meals->when($request->input('max_time'), function($query, $time) {
            $query->where('avg_time', '<=', $time);
        });

        $meals->withExists([
            'favourites as is_favourite' => function ($query) use ($user) {
                $query->where('user_id', $user->unique_id);
            }
        ]);
        $meals = $meals->paginate($this->paginate);

        return $this->returnMessageTemplate(true, '', $meals);
    }

    function vendorFetchSingleMeal(MealService $mealService, $meal_id){
        $meal = $mealService->find($meal_id)->owner()->reviews()->query();

        $orders = Order::whereJsonContains('meals', ['meal_id' => $meal_id])->get();
        $meal = $meal->first();
        $meal->orders = $orders;

        return $this->returnMessageTemplate(true, '', $meal);
    }

    function single(MealService $mealService, $meal_id){
        $meal = $mealService->find($meal_id)->owner()->reviews()->query();
        if(!$meal->exists()) return $this->returnMessageTemplate(false, "Meal Not found");
        $orders = Order::whereJsonContains('meals', ['meal_id' => $meal_id])->count();
        $meal = $meal->first();
        $meal->order_count = $orders;
        return $this->returnMessageTemplate(true, '', $meal);
    }

    function fetchMealsByAds(){
        $meals = Meal::where('promoted', $this->yes)->with(['categories', 'vendor'])->paginate($this->paginate);
        return $this->returnMessageTemplate(true, $this->returnSuccessMessage('fetched_all', 'Meal'), $meals);
    }
}
