<?php

namespace App\Services;

use App\Models\Meal\Meal;
use App\Models\User;
use App\Traits\Generics;
use App\Traits\ReturnTemplate;
use App\Models\Role\AccountRole;

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

    public function haversineDistance($lat1, $lon1, $lat2, $lon2) {
        // Convert degrees to radians
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);
    
        // Radius of the Earth in kilometers (mean value)
        $earthRadius = 6371;
    
        // Haversine formula
        $dlat = $lat2 - $lat1;
        $dlon = $lon2 - $lon1;
        $a = sin($dlat / 2) * sin($dlat / 2) + cos($lat1) * cos($lat2) * sin($dlon / 2) * sin($dlon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;
    
        return $distance;
    }    

    public function filterByGeolocation($keyword1 = 'geolocation', $keyword2 = 'radius') {
        $geolocation = request()->input($keyword1);
    
        if (!$geolocation) return $this;
    
        [$latitude, $longitude] = explode(',', $geolocation);
        $distanceInKm = request()->input($keyword2, 15);
    
        $vendors = User::where('role', AccountRole::where('name', 'Vendor')->value('unique_id'))
            ->whereNotNull('geolocation')
            ->whereNotNull('coordinates')
            // ->where('status', 'confirmed')
            // ->where('kyc_status', 'confirmed')
            ->get();   
    
        $filteredVendorIds = $vendors->filter(function ($vendor) use ($latitude, $longitude, $distanceInKm) {
            $explodedCoords = $vendor->geolocation;
            $vendorLatitude = $explodedCoords[0];
            $vendorLongitude = $explodedCoords[1];
            $distance = $this->haversineDistance($vendorLatitude, $vendorLongitude, $latitude, $longitude);
            return $distance <= $distanceInKm;
        })->pluck('unique_id');
    
       // Fetch meals associated with the filtered vendor IDs
       $this->query = Meal::whereIn('user_id', $filteredVendorIds);
    
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
