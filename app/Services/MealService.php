<?php

namespace App\Services;

use App\Models\Meal\Meal;
use App\Models\Order;
use App\Models\User;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use App\Models\Role\AccountRole;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;

class MealService {
    use ReturnTemplate, Generics;

    private $query;

    function __construct(Meal $meal = null){
        $this->query = $meal ?? Meal::query();
        return $this->query;
    }


    function query(){
        return $this->query;
    }

    function find($id, $column = 'unique_id'){
        $query = $this->query->where($column, $id);
        $this->query = $query;
        return $this;
    }

    function hasVendor($status = 'confirmed'){
        $this->query = $this->query->whereRelation('vendor', 'kyc_status', $status)->whereRelation('vendor', 'status', $status);
        return $this;
    }

    function owner(){
        $this->query = $this->query->with('vendor');
        return $this;
    }

    function category(){
        $this->query = $this->query->with('categories');
        return $this;
    }

    function byUser($user_id = null){
        $this->query = $this->query->when($user_id, function($query, $user_id){
            $query->where('user_id', $user_id);
        });
        return $this;
    }

    function orders(string|bool $count = false){
        if($count === 'withCount') $this->query = $this->query->with('orders')->withCount('orders');
        if(!$count) $this->query = $this->query->with('orders');
        if($count) $this->query = $this->query->withCount('orders');
        return $this;
    }

    function filterByCity(){
        $this->query = $this->query->when(request()->input("city"), function($query, $city){
            $query->whereRelation('vendor', 'city', $city)->orWhereRelation('vendor', 'state', $city);
        });
        return $this;
    }

    function filterByCategory($category = "category"){
        $this->query = $this->query->when(request()->input($category), function($query, $category){
            $query->where('category', $category);
        });
        return $this;
    }

    public function filterByGeolocation($keyword1 = 'geolocation', $keyword2 = 'radius') {
        $geolocation = request()->input($keyword1);
    
        if (!$geolocation) return $this;
    
        [$latitude, $longitude] = explode(',', $geolocation);
        $distanceInKm = request()->input($keyword2, 15);
    
        // Create a POINT object from latitude and longitude
        $targetLocation = DB::raw("POINT($latitude, $longitude)");

        $role = AccountRole::where('name', 'Vendor')->first();
    
        $this->query = User::selectRaw("*, ST_DISTANCE(geolocation, $targetLocation) AS distance")
            ->whereRaw("ST_DISTANCE(geolocation, $targetLocation) < $distanceInKm")
            ->orderBy('distance')
            ->where('role', optional($role)->unique_id)
            ->with('meals');
              
        return $this;
    }
    
    
    
    function status($availability = "availability"){
        $this->query = $this->query->when(request()->input($availability), function($query, $status){
            $query->where('availability', $status);
        });
        return $this;
    }

    function search($keyword = "keyword"){
        $this->query = $this->query->when(request()->input($keyword), function($query, $keyword){
            $query->where('name', 'LIKE', "%{$keyword}%");
        });
        return $this;
    }

    function orderBy($keyword = 'order'){
        $this->query = $this->query->when(request()->input($keyword), function($query, $order){
            if($order === 'rating') $query->orderBy('rating', 'desc');
            if($order === 'popular') $query->orderBy('total_orders', 'desc');
        });
        return $this;
    }

    function delete(){
        $this->query = $this->query->delete();
    }

    function isFavourite(){
        // $this->query = $this->query->
    }

    function reviews(){
        $this->query->with('reviews')->withCount('reviews');
        return $this;
    }

    function groupCategory(){
        $this->query = $this->query->groupBy('category_id');
        return $this;
    }

    function mealCategories () {
        // $this->query = $this->query->groupBy('category');
        return $this;
    }
}
