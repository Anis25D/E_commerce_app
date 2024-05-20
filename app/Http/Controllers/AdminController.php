<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Seller;
use App\Models\Order;
use App\Models\Notification;
use App\Models\Customer;


class AdminController extends Controller
{
   
    public function get_users()
    {
        $users = User::whereIn('role', ['seller', 'customer'])
                     ->with(['customer', 'seller'])
                     ->get();
    
        $usersWithModifiedValues = $users->map(function ($user) {
            $userData = [
                'id' => $user->id,
                'username' => $user->username,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'photo' => $user->photo,
                'role' => $user->role,
            ];
    
            if ($user->customer) {
                $userData['infos'] = [
                    'id' => $user->customer->id,
                    'birthday' => $user->customer->birthday,
                    'gender' => $user->customer->gender,
                    'city' => $user->customer->city,
                    'address' => $user->customer->address,
                    'postalcode' => $user->customer->postalcode,
                    'phone' => $user->customer->phone,
                ];
            }
    
            if ($user->seller) {
                $userData['infos'] = [
                    'id' => $user->seller->id,
                    'status' => $user->seller->status,
                    'phone' => $user->seller->phone,
                    'city' => $user->seller->city,
                    'location' => $user->seller->location,
                ];
            }
    
            return $userData;
        });
    
        return response()->json([
            'users' => $usersWithModifiedValues,
            'photo_base_url' => asset('public') // Add this line to send the base URL of your photo directory
        ], 200);
    }
    
    public function get_sellers()
    {
        
        $sellers = Seller::with('user')->get();

        return response()->json(['sellers' => $sellers], 200);
    }

    
    public function get_customers()
    {
        $customers = Customer::all();

        return response()->json([
            'customers' => $customers,
        ], 200);
    }


    public function get_user($userId)
{

    $user = User::with(['customer', 'seller'])->find($userId);

    
    if (!$user) {
        return response()->json([
            'message' => 'User not found',
        ], 404);
    }

   
    $userData = [
        'id' => $user->id,
        'username' => $user->username,
        'firstname' => $user->firstname,
        'lastname' => $user->lastname,
        'email' => $user->email,
        'email_verified_at' => $user->email_verified_at,
        'created_at' => $user->created_at,
        'updated_at' => $user->updated_at,
        'photo' => $user->photo,
        'role' => $user->role,
    ];

    if ($user->customer) {
        $userData['infos'] = [
            'id' => $user->customer->id,
            'birthday' => $user->customer->birthday,
            'gender' => $user->customer->gender,
            'city' => $user->customer->city,
            'address' => $user->customer->address,
            'postalcode' => $user->customer->postalcode,
            'phone' => $user->customer->phone,
        ];
    }

    if ($user->seller) {
        $userData['infos'] = [
            'id' => $user->seller->id,
            'status' => $user->seller->status,
            'phone' => $user->seller->phone,
            'city' => $user->seller->city,
            'location' => $user->seller->location,
        ];
    }

    
    return response()->json([
        'user' => $userData,
    ], 200);
}

public function get_notifications()
{
    // Retrieve the admin user
    $adminUser = User::where('role', 'admin')->first();

    // Check if the admin user exists
    if (!$adminUser) {
        // Handle the case where admin user is not found
        // You might want to log an error or throw an exception
        return response()->json(['message' => 'Admin user not found'], 404);
    }

    // Retrieve the admin user ID
    $adminUserId = $adminUser->id;

    // Retrieve notifications for the admin user and order them by created_at
    $notifications = Notification::where('user_id', $adminUserId)
                                  ->orderBy('created_at', 'desc')
                                  ->get();

    // Check if notifications exist
    if ($notifications->isEmpty()) {
        // Handle the case where no notifications are found for the admin user
        return response()->json(['message' => 'No notifications found for the admin user'], 404);
    }

    return $notifications;
}

public function mark_notifications_seen(Request $request)
{
    // Retrieve the admin user
    $adminUser = User::where('role', 'admin')->first();

    // Check if the admin user exists
    if (!$adminUser) {
        // Handle the case where admin user is not found
        // You might want to log an error or throw an exception
        return false;
    }

    // Retrieve the admin user ID
    $adminUserId = $adminUser->id;

    // Update status of notifications for the admin user as 'seen'
    Notification::where('user_id', $adminUserId)->update(['status' => 'seen']);

    return response()->json(['message' => 'Notifications marked as seen'],200);
}

 

public function get_orders(Request $request)
{
    // Fetch all orders with the customer relationship and eager load specific fields from the user relationship
    $orders = Order::with(['customer.user:id,firstname,lastname,photo'])->get();

    // Transform the orders data to include the desired customer info
    $ordersData = $orders->map(function ($order) {
        // Extract the desired user info from the loaded relationship
        $userInfo = $order->customer->user;

        // Add the user info to the order data
        $orderData = $order->toArray();
        $orderData['firstname'] = $userInfo->firstname;
        $orderData['lastname'] = $userInfo->lastname;
        $orderData['photo'] = $userInfo->photo;

        return $orderData;
    });

    return response()->json(['orders' => $ordersData], 200);
}
public function get_orders_by_status($status)
{
    // Validate the status parameter
    if (!in_array($status, ['pending', 'canceled', 'completed'])) {
        return response()->json(['error' => 'Invalid status value'], 400);
    }

    // Fetch orders based on the status
    $orders = Order::where('status', $status)
        ->with(['customer.user:id,firstname,lastname,photo'])
        ->get();

    // Transform the orders data to include the desired customer info
    $ordersData = $orders->map(function ($order) {
        // Extract the desired user info from the loaded relationship
        $userInfo = $order->customer->user;

        // Add the user info to the order data
        $orderData = $order->toArray();
        $orderData['firstname'] = $userInfo->firstname;
        $orderData['lastname'] = $userInfo->lastname;
        $orderData['photo'] = $userInfo->photo;

        return $orderData;
    });

    return response()->json(['orders' => $ordersData], 200);
}

}
