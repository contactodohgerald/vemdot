<?php

namespace App\Http\Controllers;

use App\Models\Meal\Meal;
use App\Models\Order;
use App\Models\Role\AccountRole;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Models\Withdrawal\WalletWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{


    public function index()
    {
        return view('home');
    }

    public function showDashoardPage(){
        $meals = Meal::all();
        $options = ['paid', 'processing', 'done', 'enroute', 'pickedup'];
        $orders = Order::all();
        $pendingOrders = Order::whereNotIn('status', $options)->get();
        $transactions = Transaction::where('type', 'fund_wallet')->paginate(7);
        $withdrawals = WalletWithdrawal::paginate(7);
        $vendors = User::where('role', AccountRole::where('name', 'Vendor')->value('unique_id'))->get();
        $users = User::where('role', AccountRole::where('name', 'User')->value('unique_id'))->get();
        $logistic = User::where('role', AccountRole::where('name', 'Logistic')->value('unique_id'))->get();
        
        return view('pages.dashboard')
            ->with('withdrawals', $withdrawals)
            ->with('transactions', $transactions)
            ->with('orders', $orders)
            ->with('pendingOrders', $pendingOrders)
            ->with('meals', $meals)
            ->with('vendors', $vendors)
            ->with('users', $users)
            ->with('logistic', $logistic)
            ->with('meals', $meals);
    }

    public function clearCache()
    {
        Artisan::call('cache:clear');
        return view('clear-cache');
    }
}
