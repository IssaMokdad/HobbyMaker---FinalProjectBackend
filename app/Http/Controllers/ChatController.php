<?php

namespace App\Http\Controllers;
use App\Message;
use App\Events\RealTimeChat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    
    public function getMessages(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'friend_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        $my_id = $request->input('user_id');
        $user_id = $request->input('friend_id');

        // Make read all unread message
        Message::where(['from' => $user_id, 'to' => $my_id])->update(['is_read' => 1]);

        // Get all message from selected user
        $messages = Message::where(function ($query) use ($user_id, $my_id) {
            $query->where('from', $user_id)->where('to', $my_id);
        })->orWhere(function ($query) use ($user_id, $my_id) {
            $query->where('from', $my_id)->where('to', $user_id);
        })->get();

        return response()->json(['data' => $messages]);
    }

    public function sendMessage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'friend_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
            'message' => ['required']
        ]);


        $from = $request->input('user_id');
        $to = $request->input('friend_id');
        $message = $request->input('message');

        $data = new Message();
        $data->from = $from;
        $data->to = $to;
        $data->message = $message;
        $data->is_read = 0; // message will be unread when sending message
        $data->save();
        event(new RealTimeChat($data));
        return response()->json(['data' => $data]);
    }
}
