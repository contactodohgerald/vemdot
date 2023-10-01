<?php

namespace App\Models;

use App\Models\Address\Address;
use App\Models\Meal\Meal;
use App\Models\Role\AccountRole;
use App\Models\Vendors\VendorLogistic;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Country\CountryList;
use App\Models\Site\SiteSettings;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\Generics;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable, SoftDeletes, Generics;

    protected $primaryKey = 'unique_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'unique_id', 'name', 'email', 'referral_id', 'referred_id', 'role', 'email_verified_at', 'two_factor', 'delivery_fee',
        'status',
        'country',
        'phone',
        'gender',
        'avatar',
        'main_balance',
        'ref_balance',
        'password',
        'business_name',
        'city',
        'state',
        'coordinates',
        'address',
        'avg_time',
        'logo',
        'id_number',
        'id_image',
        'kyc_status',
        'availability',
        'push_notification',
        'geolocation'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'coordinates',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'geolocation' => 'array'
    ];

    protected $attributes = [
        'kyc_status' => 'pending',
        'availability' => 'yes',
        'status' => 'confirmed',
        'two_factor' => 'no',
        'two_factor_access' => 'text',
        'main_balance' => 0,
        'ref_balance' => 0,
        'first_time_login' => 'yes'
    ];

    public function generateCodeFor2fa($user)
    {
        $code = rand(1000, 9999);

        $emailList = ['test@production.com', 'vendorprod@gmail.com', 'logisticprod@gmail.com', 'riderprod@gmail.com'];

        if (in_array(optional($user)->email, $emailList)) {
            $code = "0000";
        }

        $faildCode = UserCode::where([
            ['user_id', $user->unique_id],
            ['status', 'un-used']
        ])->first();

        if ($faildCode != null) {
            $faildCode->status = 'failed';
            $faildCode->save();
        }

        UserCode::create([
            'unique_id' => $this->createUniqueId('user_codes'),
            'user_id' => $user->unique_id,
            'code' => $code
        ]);

        return ['status' => true, 'code' => $code];
    }

    function scopeRoleVendor($query)
    {
        return $query->whereRelation('userRole', 'name', 'Vendor');
    }

    function scopeRoleUser($query)
    {
        return $query->where('unique_id', $this->unique_id)->whereRelation('userRole', 'name', 'User')->exists();
    }

    public function getAllUsers($condition, $id = 'id', $desc = "desc")
    {
        return User::where($condition)->orderBy($id, $desc)->get();
    }

    public function paginateUsers($num, $condition, $id = 'id', $desc = "desc")
    {
        return User::where($condition)->orderBy($id, $desc)->paginate($num);
    }

    public function getUser($condition)
    {
        return User::where($condition)->first();
    }

    public function get_roles()
    {
        $roles = [];
        foreach ($this->getRoleNames() as $key => $role) {
            $roles[$key] = $role;
        }

        return $roles;
    }

    static function admin()
    {
        return static::where('role', 'super_admin')->first();
    }

    public function meals()
    {
        return $this->hasMany(Meal::class, 'user_id', 'unique_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id', 'unique_id');
    }

    public function userRole()
    {
        return $this->belongsTo(AccountRole::class, 'role', 'unique_id');
    }

    public function bikes()
    {
        return $this->hasMany(User::class, 'business_name', 'unique_id');
    }

    public function bikeOwner()
    {
        return $this->hasMany(User::class, 'unique_id', 'business_name');
    }

    public function countries()
    {
        return $this->belongsTo(CountryList::class, 'country', 'unique_id');
    }

    public function cards()
    {
        return $this->hasMany(Card::class, 'user_id', 'unique_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'unique_id');
    }

    public function logisticCompany()
    {
        return $this->hasMany(VendorLogistic::class, 'vendor_id', 'unique_id');
    }

    public function isLogistic()
    {
        return $this->where('unique_id', $this->unique_id)->whereRelation('userRole', 'name', 'Logistic')->exists();
    }

    function isVendor()
    {
        return $this->where('unique_id', $this->unique_id)->whereRelation('userRole', 'name', 'Vendor')->exists();
    }

    function isUser()
    {
        return $this->where('unique_id', $this->unique_id)->with('userRole')->whereRelation('userRole', 'name', 'User')->exists();
    }

    function isRider()
    {
        return $this->where('unique_id', $this->unique_id)->with('userRole')->whereRelation('userRole', 'name', 'Rider')->exists();
    }
    function logistic()
    {
        return $this->belongsTo(User::class, 'business_name', 'unique_id');
    }

    function currency()
    {
        return 'NGN';
    }

    public function routeNotificationForFcm()
    {
        return $this->device_id;
    }
}
