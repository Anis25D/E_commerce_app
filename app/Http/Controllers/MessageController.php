<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Events\TestEvent;
use App\Events\MessageAlert;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Dispatch a test event.
     *
     * @return \Illuminate\Http\Response
     */
    public function testEvent()
    {
        // Data to include in the test event payload
        // $data = ['message' => 'This is a test message'];

        // Dispatch the TestEvent with the data payload
   
        // event(new TestEvent($data));
        // event(new MyEvent('hello world'));
        event(new MessageAlert('seller up'));

        // Return a response
        return response()->json(['message' => 'Test event dispatched successfully'], 200);
    }

    public function sendMessage(Request $request)
    {
        // Validate the incoming request data
        
      
    // Determine the sender ID based on the request parameter or authenticated user
    $senderId = $request->has('sender_id') ? $request->input('sender_id') : auth()->id();

    // Determine the recipient ID based on the request (customer or seller)
    $recipientId = $request->has('seller_id') ? $request->input('seller_id') : $request->input('customer_id');

    // Store the new message in the database
    $message = Message::create([
        'content' => $request->input('content'),
        'sender_id' => $senderId,
        'customer_id' => $request->input('customer_id'),
        'seller_id' => $request->input('seller_id'),
        'status' => 'unseen',
    ]);

    // Broadcast the new message to the recipient
    // broadcast(new MessageAlert($message))->toOthers()->where('recipient_id', $recipientId);
    // broadcast(new MessageAlert($recipientId))->toOthers();   
    // broadcast(new MessageAlert($message))->toOthers()->onConnection('pusher')->to("user.{$recipientId}");
    broadcast(new MessageAlert($message))->toOthers();
    // Optionally, return a response indicating success or redircess or redirect to a new page
    return response()->json(['message' => $recipientId], 200);
    // Broadcast the new message to the recipient
   
    
}
}