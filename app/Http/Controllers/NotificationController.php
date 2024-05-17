<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{

    public function createNotification(Request $request)
    {

        $userId = $request->user_id; 

        Notification::create([
            'user_id' => $userId,
            'type' => 'has registered as a seller.', 
            'status' => 'unseen', 
            'content' => 'new_seller',
            'photo' => '7RuUr0zpYuMOiFRSDbMBaDcIYbFmTNu0dOd81RI9.jpg',
        ]);
    
        return response()->json(['message' => 'Notification created successfully'], 201);
     
}

public function markNotificationSeen($notificationId)
{
    // Find the notification by its ID
    $notification = Notification::find($notificationId);

    // Check if the notification exists
    if (!$notification) {
        // Handle the case where notification is not found
        // You might want to log an error or throw an exception
        return response()->json(['message' => 'Notification not found'], 404);
    }

    // Update the status of the notification to 'seen'
    $notification->update(['status' => 'seen']);

    return response()->json(['message' => 'Notification marked as seen'], 200);
}
    
}
