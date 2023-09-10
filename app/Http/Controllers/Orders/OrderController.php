<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\CreateOrderRequest;
use App\Http\Requests\Api\Order\OrderStatusRequest;
use App\Models\Address\Address;
use App\Models\Order;
use App\Models\Site\SiteSettings;
use App\Models\User;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use RealRashid\SweetAlert\Facades\Alert;
use Carbon\Carbon;

class OrderController extends Controller
{

    function create(CreateOrderRequest $request, OrderService $orderService) 
    {
        $user = $this->user();

        $isHomeDelivery = $request->filled("courier_id") && ($request->delivery_method === 'home');
        $courier = $request->filled('courier_id') ? User::find($request->courier_id) : null;

        if($courier) {
            if(!$courier->isLogistic()) return $this->returnMessageTemplate(false, "The Selected Courier is not registered as a Logistic Comapany");
        }

        if(!User::find($request->vendor_id)->isVendor()) return $this->returnMessageTemplate(false, "The Selected Vendor is not registered as a Vendor");

        try {
            $meals = $orderService->confirmMeals($request->meals, $request->vendor_id);
        } catch (\Throwable $th) {
            return $this->returnMessageTemplate(false, $th->getMessage());
        }

        $address = $request->address_id ? Address::find($request->address_id)->location : $request->receiver_location; // Handle Address
        $settings = SiteSettings::first();

        // Calculate Delivery fee per Kilometer
        // $fee = $isHomeDelivery ? ($courier->delivery_fee ?: $settings->delivery_fee) : 0;
        $fee = $isHomeDelivery ? $courier->delivery_fee : 0;
        // $delivery_fee =  $isHomeDelivery ? $fee * $request->delivery_distance : 0;
        $delivery_fee =  $isHomeDelivery ? $fee : 0;
        $delivery_time = $isHomeDelivery ? $meals->max('time') : 0;

        $reference = $this->createRandomNumber(6);

        if($request->payment_method == "wallet"){
            $val = (ceil($meals->sum('price')) + $delivery_fee);
            
            if($user->main_balance < (ceil($meals->sum('price')) + $delivery_fee)) 
                return $this->returnMessageTemplate(false, "Your Wallet balance is insufficent to complete this transaction.");   
        }

        $order = Order::create($request->safe()->merge([
            'user_id' => $user->unique_id,
            'vendor_id' => $request->vendor_id,
            'unique_id' => $this->createUniqueId('orders'),
            'meals' => $meals,
            'receiver_name' => $request->receiver_name ?? $user->name,
            'receiver_phone' => $request->receiver_phone ?? $user->phone,
            'receiver_location' => $address,
            'receiver_email' => $request->receiver_email ?? $user->email,
            'amount' => ceil($meals->sum('price')), //Calculate the price by sum
            'delivery_fee' => $delivery_fee,
            'avg_time' => $delivery_time,
            'reference' => $reference,
            'courier_id' => $isHomeDelivery ? $courier->unique_id : null
        ])->except('address_id')); // Create the order

        $transaction = $orderService->createOrderTransaction($order, $request); // Create Transaction for this Order
        
        $orderService->sendOrderUpdateNotification($order, $user, [
            'order' => $order,
            'cardPaymentUrl' => ''
        ]);

        $orderService->sendOrderNotification($order, $user);

        if($order->reciever_id) {
            $reciever = User::find($order->reciever_id);
            // if($reciever) $orderService->sendOrderUpdateNotification($order, $user);
        }

        return $orderService->initiatePayment($request, $transaction, $user, $order);
    }
    
      function assignRiderToOrder(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'bike_id' => 'required|exists:users,unique_id',
            'order_id' => 'required|exists:orders,unique_id'
        ]);

        $order = Order::with(['orderStatus', 'vendor', 'courier', 'bike', 'user'])->find($request->order_id);

        if ($order->courier_id !== $user->unique_id) return $this->returnMessageTemplate(false, "The selected order is not assigned to the current user.");

        $rider = User::with(['logistic', 'userRole'])->find($request->bike_id);

        if (!$rider->isRider()) return $this->returnMessageTemplate(false, "The Selected Bike is not a registered as a rider.");

        if ($rider->logistic->unique_id !== $user->unique_id) return $this->returnMessageTemplate(false, "The Selected rider is not assigned to the current Courier");
    
        $order->bike_id = $rider->unique_id;
        $order->riderStatus = 'pending';
        $order->save();
        
        return $this->returnMessageTemplate(true, "Order assigned to a rider successfully!");
    }

    function update(OrderStatusRequest $request, OrderService $orderService, $order_id) 
    {
        
        if(!$order = Order::find($order_id)) return $this->returnMessageTemplate(false, $this->returnErrorMessage('not_found', 'The Order'));
        
        $status = $request->status;

        // if($order->status === $status) return $this->returnMessageTemplate(false, "Order Status is already set to ".ucfirst($status));
        
        $user = $this->user();

        // $canCancel = $orderService->checkPreviousCancelledOrders($user, $status);
        //     if(!$canCancel[0]) return $this->returnMessageTemplate(false, $canCancel[1]);

        if(!$orderService->checkCorrectUser($user, $order, $status) || !$this->checkUserAbility($user, $order)) return $this->returnMessageTemplate(false, "You cannot set this order to ".ucfirst($status));
 
        // $notUpdateable = $orderService->canUpdate($user, $order, $status);

        // if(!$notUpdateable)
        //     return $this->returnMessageTemplate(false, "Order Update failed because it is set to ".ucfirst($order->status), ['order' => $order]);
        
        $vendorCannotUpdateDelivered = (($user->userRole->name === 'Vendor') && ($status === 'delivered') && ($order->delivery_method !== 'pickup'));
        if($vendorCannotUpdateDelivered) return $this->returnMessageTemplate(false, "You cannot update this order's status to $status");
      
        $orderService->handleStatusUpdate($order, $user, $status);
        $order = Order::with(['orderStatus', 'vendor', 'courier', 'bike', 'user'])->find($order->unique_id);

        $orderService->sendOrderUpdateNotification($order, $user, $order);

        return $this->returnMessageTemplate(true, "Order Status has been updated successfully", $order);
    }

    function checkUserAbility($user, $order){
        if(!$user) return false;
        if($user->isLogistic()) return $order->courier_id === $user->unique_id;
        if($user->isRider()) return $order->bike_id === $user->unique_id;
        if($user->isVendor()) return $order->vendor_id === $user->unique_id;
        if($user->isUser()) return $order->user_id === $user->unique_id;
    }

    function list(Request $request, $user_id = null)
    {
        $user = User::find($user_id) ?? $this->user();
        $query = Order::query();

        if(!$user) return $this->returnMessageTemplate(false, "User not found");

        $query->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->get('status'));
        });

        $query->when($user->isLogistic(), function ($query) use ($user) {
            $query->where('courier_id', $user->unique_id);
        });

        $query->when($user->isRider(), function ($query) use ($user) {
            $query->where([
                'bike_id' => $user->unique_id,
                'delivery_method' => 'home'
            ]);
        });

        $query->when($user->isVendor(), function ($query) use ($user) {
            $query->where('vendor_id', $user->unique_id);
        });

        $query->when($user->isUser(), function ($query) use ($user) {
            $query->where('user_id', $user->unique_id)->orWhere('receiver_id', $user->unique_id)->orWhere('receiver_email', $user->email);
        });

        $query->where('status', '!=', $this->pending);

        $query->with(['orderStatus', 'vendor', 'courier', 'bike', 'user']);

        return $this->returnMessageTemplate(true, '', $query->get());
    }

    function show($order_id)
    {
        $order = Order::with(['orderStatus', 'vendor', 'courier', 'bike', 'user'])->findorFail($order_id);
        return $this->returnMessageTemplate(true, '', $order);
    }

    function mealOrders($meal_id) {
        $order = Order::whereJsonContains('meals', ['meal_id' => $meal_id])->with(['orderStatus', 'vendor', 'courier', 'bike', 'user']);
        return $this->returnMessageTemplate(true, '', $order->get());
    }

    function downloadInvoice($reference) {
        $order = Order::where(['reference' => $reference])->first();
        $vendor = User::find($order->vendor_id);
        $user = User::find($order->user_id);
        $interval = CarbonInterval::minutes($order->avg_time);
        $avg_time = CarbonInterval::make($interval)->cascade()->forHumans(['short' => true]);

        $data = [
            'vendor' => $vendor,
            'user' => $user,
            'order' => $order,
            'date' => Date::parse($order->created_at)->format('jS, F Y'),
            'avg_time' => $avg_time
        ];

        $pdf = Pdf::loadView('emails.order-email', $data);
        return $pdf->download('download.pdf');
    }

    protected function getOnGoingOrder($startDate = null, $endDate = null) {
        $options = ['cancelled', 'declined', 'terminated', 'failed', 'delivered'];
        $order = Order::whereNotIn('status', $options)
            ->orderBy('id', 'desc')
            ->paginate($this->paginate);

        //if the start date and end date are not null add the
        if ($startDate !== null && $endDate !== null) {
            $order = Order::whereNotIn('status', $options)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<', $endDate)
                ->orderBy('id', 'desc')
                ->paginate($this->paginate);
        }
        if ($startDate != null) {
            $order = Order::where('status', $startDate)
                ->orderBy('id', 'desc')
                ->paginate($this->paginate);
        }
        //return $order;
        $payload = [
            'orders' => $order,
        ];
        return view('pages.order.ongoing-order', $payload);
    }
    protected function getOngoingOrderByDate(Request $request) {
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('orders/interface/' . $startDate . '/' . $endDate);
    }

    protected function getOngoingOrderByType(Request $request) {
        $type = $request->user_type;
        return redirect()->to('orders/interface/' . $type);
    }

    //get the list of order history
    protected function getOnFinishedOrder($startDate = null, $endDate = null) {
        $options = ['paid', 'processing', 'done', 'enroute', 'pickedup'];
        $order = Order::whereNotIn('status', $options)
            ->orderBy('id', 'desc')
            ->paginate($this->paginate);

        //if the start date and end date are not null add the
        if ($startDate !== null && $endDate !== null) {
            $order = Order::whereNotIn('status', $options)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<', $endDate)
                ->orderBy('id', 'desc')
                ->paginate($this->paginate);
        }
        if ($startDate != null) {
            $order = Order::where('status', $startDate)
                ->orderBy('id', 'desc')
                ->paginate($this->paginate);
        }
        //return $order;
        $payload = [
            'orders' => $order,
        ];
        return view('pages.order.order-history', $payload);
    }
    protected function getOrderByDate(Request $request) {
        $startDate = Carbon::parse($request->start_date)->toDateString();
        $endDate = Carbon::parse($request->end_date)->toDateString();
        return redirect()->to('orders/history/interface/' . $startDate . '/' . $endDate);
    }
    protected function getOrderByType(Request $request) {
        $type = $request->user_type;
        return redirect()->to('orders/history/interface/' . $type);
    }

    protected function terminateOrder(OrderService $orderService, Request $request) {
        $response = $orderService->updateOrderStatus($request->unique_id, $this->failed);
        if (!$response) {
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Order'));
            return redirect()->back();
        }
        Alert::success('Success', $this->returnSuccessMessage('updated', 'Order'));
        return redirect()->back();
    }

    protected function deleteOrder(OrderService $orderService, Request $request) {
        $response = $orderService->deleteOrder($request->unique_id);
        if (!$response) {
            Alert::error('Error', $this->returnErrorMessage('not_found', 'Order'));
            return redirect()->back();
        }
        Alert::success('Success', $this->returnSuccessMessage('deleted', 'Order'));
        return redirect()->back();
    }
}
