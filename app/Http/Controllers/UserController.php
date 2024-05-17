<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Seller;
use App\Models\Customer;
use App\Events\MessageAlert;


class UserController extends Controller
{


    public function deleteUser($userId)
    {
        $user = User::find($userId);
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $user->delete();
    
        return response()->json(['message' => 'User deleted successfully'], 200);
    }

   
    public function updateUser(Request $request, $id)
    {
        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            // If the user doesn't exist, return a 404 response
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate the request data for user update
        $validatedUserData = $request->validate([
            // Define validation rules for user fields
            'username' => 'string|max:255',
            'firstname' => 'string|max:255',
            'lastname' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $id, // Unique email except for the current user
            // Add more user fields as needed
        ]);

        // Update the user with the validated data
        $user->update($validatedUserData);

        // Check the role of the user
        if ($user->role === 'customer') {
            // If the user is a customer, update customer information

            // Find the associated customer record
            $customer = Customer::where('user_id', $id)->first();

            // Check if the customer exists
            if ($customer) {
                // Validate the request data for customer update
                $validatedCustomerData = $request->validate([
                    // Define validation rules for customer fields
                    'birthday' => 'date',
                    'gender' => 'string|max:255',
                    'city' => 'string|max:255',
                    'address' => 'string|max:255',
                    'postalcode' => 'string|max:255',
                    'phone' => 'string|max:255',
                    // Add more customer fields as needed
                ]);

                // Update the customer with the validated data
                $customer->update($validatedCustomerData);
            }
        } elseif ($user->role === 'seller') {
            // If the user is a seller, update seller information

            // Find the associated seller record
            $seller = Seller::where('user_id', $id)->first();

            // Check if the seller exists
            if ($seller) {
                // Validate the request data for seller update
                $validatedSellerData = $request->validate([
                    // Define validation rules for seller fields
                    'status' => 'string|max:255',
                    'phone' => 'string|max:255',
                    'city' => 'string|max:255',
                    'location' => 'string|max:255',
                    // Add more seller fields as needed
                ]);

                // Update the seller with the validated data
                $seller->update($validatedSellerData);
            }
        }

        // event(new MessageAlert('hello world'));
        // Return a success response
        return response()->json(['message' => 'User and associated information updated successfully', 'user' => $user], 200);
    }

}
