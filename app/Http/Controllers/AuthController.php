<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Seller;
use App\Models\Customer;
use App\Models\Notification;
use App\Events\NewSeller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{

    // public function register(Request $request){

    //     // event(new NewSeller('seller up'));

    //     DB::beginTransaction();

    //     try {


    //         $photoPath = null;
    //         if ($request->hasFile('photo')) {
    //             $photoPath = $request->file('photo')->store('photos', 'public');
    //         }

    //         $user = User::create([
    //             'username' => $request->username,
    //             'firstname' => $request->firstname,
    //             'lastname' => $request->lastname,
    //             'email'=> $request->email,
    //             'password'=> bcrypt($request->password),
    //             'role'=> $request->role,
    //             // 'photo'=>$request->photo,
    //             'photo' => $photoPath,
    //         ]);
    
    //         $user_id = $user->id;
    
    //         $token = $user->createToken($request->email)->plainTextToken;
    
    //         $role = $request->role;
    
    //         switch($role) {
    //             case 'seller':
    //                 $seller = Seller::create([
    //                     'user_id' => $user_id,
    //                     'status' => 'pending',
    //                     'birthday' => $request->birthday,
    //                     'phone' => $request->phone,
    //                     'city'=> $request->city,
    //                     'location'=> $request->location,
    //                 ]);

    //                 broadcast(new NewSeller($seller))->toOthers();

    //                 break;
    
    //             case 'customer':
    //                 $customer = Customer::create([
    //                     'user_id' => $user_id,
    //                     'gender' => $request->gender,
    //                     'address' => $request->address,
    //                     'phone' => $request->phone,
    //                     'postalcode' => $request->postalcode,
    //                     'birthday' => $request->birthday,
    //                     'city'=> $request->city,
    //                 ]);
    //                 break;
    //         }
    
    //         DB::commit();
    
    //         return response()->json([
    //             'token' => $token,
    //             'user' => $user,
    //             'seller' => $seller ?? null,
    //             'customer' => $customer ?? null,
    //         ], 201);

           

    //     } catch (\Exception $e) {

    //         DB::rollback();
    //         return response()->json(['message' => $e], 500);
    //     }

       

    // }

    public function registerAdmin(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $user = User::create([
                'username' => $request->username,
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'admin',
                'photo' => null,
            ]);
    
            DB::commit();
    
            return response()->json([
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => $e], 500);
        }
    }


    public function registerSeller(Request $request)
{
    DB::beginTransaction();

    try {
        $adminUser = User::where('role', 'admin')->first();

        if ($adminUser) {
         $adminUserId = $adminUser->id;
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $user = User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'seller',
            'photo' => $photoPath,
        ]);

        $seller = Seller::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'birthday' => $request->birthday,
            'phone' => $request->phone,
            'city' => $request->city,
            'location' => $request->location,
        ]);

        
        $notification = Notification::create([
            'user_id' => $adminUserId,
            'type' => 'has registered as a seller.', 
            'status' => 'unseen', 
            'content' =>  $request->firstname . ' ' . $request->lastname,
            'photo' => $photoPath,
            ]);


        broadcast(new NewSeller($user,$notification))->toOthers();

        DB::commit();

        return response()->json([
            'user' => $user,
            'seller' => $seller,
            'alert' => $notification
        ], 201);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['message' => $e], 500);
    }
}

public function registerCustomer(Request $request)
{
    DB::beginTransaction();

    try {
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
        }

        $user = User::create([
            'username' => $request->username,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'customer',
            'photo' => $photoPath,
        ]);

        $customer = Customer::create([
            'user_id' => $user->id,
            'gender' => $request->gender,
            'address' => $request->address,
            'phone' => $request->phone,
            'postalcode' => $request->postalcode,
            'birthday' => $request->birthday,
            'city' => $request->city,
        ]);

        DB::commit();

        return response()->json([
            'user' => $user,
            'customer' => $customer,
        ], 201);
    } catch (\Exception $e) {
        DB::rollback();
        return response()->json(['message' => $e], 500);
    }
}


public function login(Request $request)
{
    $credentials = $request->only(['email', 'password']);
    
    if (!Auth::attempt($credentials)) {
        return response()->json([
            'error' => 'invalid credentials'
        ], 401);
    }

    $user = Auth::user();
    $token = $user->createToken($request->email)->plainTextToken;
    $role = $user->role;

    $additionalData = [];

    if ($role == 'seller') {
        $seller = Seller::where('user_id', $user->id)->first();
        if ($seller) {
            $additionalData['role_id'] = $seller->id;
        }
    } elseif ($role == 'customer') {
        $customer = Customer::where('user_id', $user->id)->first();
        if ($customer) {
            $additionalData['role_id'] = $customer->id;
        }
    }

    return response()->json(array_merge([
        'token' => $token,
        'user' => $user,
        'role' => $role
    ], $additionalData));
}




    // public function login(Request $request){
    //     $credentials = $request->only(['email','password']);
        
    //     if(!Auth::attempt($credentials)){
    //         return response()->json([
    //             'error'=>'invalid credentials'
    //         ],401);
    //     }

    //     $user = Auth::user();
    //     $token = $user->createToken($request->email)->plainTextToken;
    //     $role = $user->role;

    //     return response()->json([
    //         'token'=>$token,
    //         'user'=>$user,
    //         'role'=>$role
    //     ]);
    // }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        
       return response()->json([
            'message' => 'User is logged out successfully'
            ], 200);
    }

    //
}
