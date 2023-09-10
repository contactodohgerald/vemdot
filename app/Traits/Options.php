<?php

namespace App\Traits;

use App\Models\Order;

trait Options {

    public $paginate = 12;
    public $dailySpecial = 24;
    public $active = 'active';
    public $pending = 'pending';
    public $blocked = 'blocked';
    public $confirmed = 'confirmed';
    public $success = 'success';
    public $inprogress = 'inprogress';
    public $verified = 'verified';
    public $suspended = 'suspended';
    public $settled = 'settled';
    public $expired = 'expired';
    public $processing = 'processing';
    public $failed = 'failed';
    public $delivered = 'delivered';
    public $declined = 'declined';
    public $cancelled = 'cancelled';
    public $yes = 'yes';
    public $no = 'no';
    public $paid = 'paid';
    public $unread = 'unread';
    public $read = 'read';

    public $orderProgression = ['cash', 'paid', 'cancelled', 'declined', 'processing', 'terminated', 'done', 'enroute', 'pickedup', 'returned', 'delivered', 'accepted', 'rejected'];

    public $orderUserActions = [
        'User' => ['cancelled'],
        'Vendor' => ['declined', 'processing', 'done', 'delivered', 'terminated'],
        'Logistic' => ['enroute', 'pickedup', 'returned', 'delivered'],
        'Rider' => ['enroute', 'pickedup', 'returned', 'delivered', 'accepted', 'rejected'],
        'Super Admin' => ['enroute', 'pickedup', 'returned', 'delivered', 'cancelled', 'declined', 'processing', 'done', 'terminated'],
    ];

    public $orderNotificationReceivers = [
        'User' => ['cash', 'declined', 'processing', 'terminated', 'pickedup', 'delivered', 'returned'],
        'Vendor' => ['paid', 'enroute', 'delivered'],
        'Rider' => ['processing', 'paid', 'done', 'terminated']
    ];

    public function formatOrderMessages($order, $user, $vendor, $logistics){
        $messages = [
            'paid' => [
                "You have received a new food order"
            ],
            'cancelled' => ""
        ];
    }

    function userMessages ($status){
        $messages = [
            'declined' => $this->formatMessagesToArray('Order Delined', "Your order has been declined by the vendor!"),
            'processing' => $this->formatMessagesToArray('Order in Progress', 'Your Order has been accepted by the vendor and is currently in progress'),
            'terminated' => $this->formatMessagesToArray('Order Terminated', 'Your Order has been terminated by the Vendor'),
            'pickedup' => $this->formatMessagesToArray('Your order has been completed', 'Your Order has been picked up successfully! Enjoy your meal!'),
            'delivered' => $this->formatMessagesToArray('Order Delivered', 'Your Order has been marked as delivered by the rider! Enjoy your Meal'),
            'returned' => $this->formatMessagesToArray('Order delivery failed', "Your Order could not be delivered by the courier"),
        ];
        return $messages[$status] ?? null;
    }

    function vendorMessages($status) {
        $messages = [
            'paid' => $this->formatMessagesToArray('You have a new Order', 'You have received a new order!'),
            'enroute' => $this->formatMessagesToArray('The rider is on their way', 'The Rider is on their way to pick up your order!'),
            'delivered' => $this->formatMessagesToArray('delivered', 'Your Order has been delivered by the Courier')
        ];
        return $messages[$status] ?? null;
    }

    function riderMessages($status){
        $messages = [
            'processing' => $this->formatMessagesToArray('New Order Delivery', 'You have a new order delivery in progress.'),
            'done' => $this->formatMessagesToArray('Order ready for delivery', 'The Order is ready and waiting for pickup'),
            'terminated' => $this->formatMessagesToArray('The Order has been terminated', 'The waiting Order has been terminated by the Vendor')
        ];
        return $messages[$status] ?? null;
    }
    
    function logisticMessages ($status){
        $messages = [
            'processing' => $this->formatMessagesToArray('New Order Delivery', 'You have a new order delivery in progress.'),
            'done' => $this->formatMessagesToArray('Order ready for delivery', 'The Order is ready and waiting for pickup. Please assign it to a rider.'),
            'terminated' => $this->formatMessagesToArray('The Order has been terminated', 'The waiting Order has been terminated by the Vendor'),
            'accepted' => $this->formatMessagesToArray("Order Accepted", "The Order has ben accepted by the assigned rider"),
            'rejected' => $this->formatMessagesToArray("Order Rejected", "The Order has ben rejected by the assigned rider! Please reassign this order to another rider.")
        ];
        return $messages[$status] ?? null;
    }


    function formatMessagesToArray($title, $message){
        return [
            'title' => $title,
            'message' => $message
        ];
    }

}


