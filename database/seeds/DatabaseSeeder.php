<?php

use App\Models\Country\CountryList;
use App\Models\Site\SiteSettings;
use App\Models\Address\Address;
use App\Models\Bank\BankDetail;
use App\Models\Meal\Meal;
use App\Models\Meal\MealCategory;
use App\Models\Plan\SubscriptionPlan;
use App\Models\Role\AccountRole;
use App\Models\User;
use App\Traits\Generics;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder{
    use Generics;
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(){
        $email = "support@".env('APP_DOMAIN');
        $user = new User();
        if(!User::where("email", $email)->exists()){
            $user->unique_id  = $this->createUniqueId('users');
            $user->name = "Super Admin";
            $user->email = $email;
            $user->role = "super_admin";
            $user->phone = "08106362992";
            $user->gender = "male";
            $user->referral_id = $this->createUniqueId('users', 'referral_id');
            $user->password = Hash::make(1234567890);
            $user->save();
        }

        
        $address = new Address();
        $address->unique_id  = $this->createUniqueId('addresses');
        $address->user_id = $user->unique_id;
        $address->city = "oshodi";
        $address->state = "Los Angels";
        $address->location = "13, Osemene close mafoluku oshodi";
        $address->default = "yes";
        $address->save();

        $bank = new BankDetail();
        $bank->unique_id  = $this->createUniqueId('bank_details');
        $bank->user_id = $user->unique_id;
        $bank->bank_id = "044";
        $bank->account_name = "Test Account";
        $bank->account_no = "0923097121";
        $bank->save();

        $countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");

        foreach ($countries as $item){
            $country = new CountryList();
            $country->unique_id  = $this->createUniqueId('country_lists');
            $country->name = $item;
            $country->save();
        }

        $categories = ["Pastries", "Soup", "Vegetables", "Native Dish", "Fast Food"];
        foreach ($categories as $item){
            $category = new MealCategory();
            $category->unique_id  = $this->createUniqueId('meal_categories');
            $category->name = $item;
            $category->status = 'active';
            $category->description = $item;
            $category->save();
        }

        $plans = ["Basic", "Flat", "Advance", "Premium", "Contract"];
        foreach ($plans as $item){
            $plan = new SubscriptionPlan();
            $plan->unique_id  = $this->createUniqueId('subscription_plans');
            $plan->name = $item;
            $plan->status = 'active';
            $plan->description = $item;
            $plan->save();
        }

        $settings = new SiteSettings();
        $settings->unique_id  = 'OGU9ZhIK0e66e8b70e91fea8';
        $settings->site_name = env('APP_NAME');
        $settings->site_email = "support@".env('APP_DOMAIN');
        $settings->site_phone = "+44 7887 443155";
        $settings->site_address = "United Kingdom: Ægisgardur 5, Reykjavik's Old Harbor";
        $settings->site_domain = env('APP_URL');
        $settings->save();

        $roles = ["Super Admin", "Admin", "Vendor", "Logistic", "Rider", "User"];

        foreach ($roles as $item){
            $role = new AccountRole();
            $role->unique_id  = $this->createUniqueId('account_roles');
            $role->name = $item;
            $role->save();
        }

    }
}
