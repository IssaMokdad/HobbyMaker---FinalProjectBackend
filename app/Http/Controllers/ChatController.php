<?php

namespace App\Http\Controllers;
use App\Message;
use App\Events\RealTimeChat;
use Illuminate\Http\Request;
use Validator;
use DB;
use App\User;
use App\Http\Controllers\FriendController;

class ChatController extends Controller
{
    public function getAllUnreadMessagesForNotifications(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $unreadMessages = DB::select("select users.id, users.first_name, users.last_name, users.image, count(is_read) as unread 
        from users JOIN messages ON users.id = messages.from and is_read = 0 and messages.to = " . $request->input('user_id') . "
        where users.id != " . $request->input('user_id') . " 
        group by users.id, users.first_name, users.last_name, users.image");

        return response()->json(['data' => $unreadMessages ]);

    }
    public function getAllUnreadMessages(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        // $unreadMessages = Message::where('to',$request->input('user_id'))
        // ->where('is_read', 0)->get();
        //get all unread messages count with friend data if unread messages exist or not
        //get all friends with the last message in chat with the unread messages
        $my_id = $request->input('user_id');
        
        // return response()->json(['data' => $friends_ids]);
        $friends = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.friend_id')
            ->where('friends.status', 'accepted')
            ->where('friends.user_id', $request->input('user_id'))
            ->get();
        $friends_ids = array_column($friends->toArray(), 'friend_id');
        // $unreadMessage= DB::select("select users.id as friend_id, users.first_name, users.last_name, users.image, count(is_read) as unread 
        // from users Left JOIN messages ON users.id = messages.from and is_read = 0 and messages.to = " . $request->input('user_id') . "
        // where users.id != " . $request->input('user_id') . " 
        // group by friend_id, users.first_name, users.last_name, users.image");

        $messages = [];
       
        foreach($friends_ids as $friend_id){

        $message = Message::where(function ($query) use ($my_id, $friend_id) {
            $query->where('from', $my_id)->where('to', $friend_id);
        })->orWhere(function ($query) use ($my_id, $friend_id) {
            $query->where('from', $friend_id)->where('to', $my_id);
        })->latest()->first();
        
        array_push($messages, $message);
        
        }
        $unread_messages_count = [];
        foreach($friends_ids as $friend_id){
        $unread_message_count = Message::where('to', $my_id)
                                ->where('from', $friend_id)
                                ->where('is_read', 0)
                                ->count();
        array_push($unread_messages_count, $unread_message_count);
    }

        return response()->json(['data' => $friends, 'last_messages'=>$messages, 'unread_messages_count'=>$unread_messages_count]);

    }

    public function markMessagesAsRead(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        Message::where( 'to' , $request->input('user_id'))->update(['is_read' => 1]);

        return response()->json(['message' => 'success']);
    }


    public function markMessagesAsReadPerUser(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
            'friend_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        Message::where( 'to' , $request->input('user_id'))
        ->where('from',$request->input('user_id'))
        ->update(['is_read' => 1]);



        return response()->json(['message' => 'success']);
    }

    // public function getLastMessage(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'user_id' => ['required', 'integer', 'min:1'],
    //         'friend_id' => ['required', 'integer', 'min:1'],
    //     ]);
    //     if ($validator->fails()) {
    //         return response()->json($validator->messages(), 419);
    //     }
    //     $user_id=$request->input('friend_id');
    //     $my_id = $request->input('user_id');
    //     $messages = [];
    //     $friends_ids = array_column(User::find($user_id)->friend->toArray(), 'friend_id');
    //     foreach($friends_ids as $friend_id){

    //     $message = Message::where(function ($query) use ($my_id, $friend_id) {
    //         $query->where('from', $my_id)->where('to', $friend_id);
    //     })->orWhere(function ($query) use ($my_id, $friend_id) {
    //         $query->where('from', $friend_id)->where('to', $my_id);
    //     })->latest()->first();
    //     array_push($messages, $message);}

    //     return response()->json(['last_message' => $messages]);
    // }


    public function getMessages(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'friend_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        $my_id = $request->input('user_id');
        $user_id = $request->input('friend_id');

        // Make read all unread message
        Message::where(['from' => $user_id, 'to' => $my_id])->update(['is_read' => 1]);

        // Get all messages between the authenticated user and friend when selected and info from about friend
        $messages = Message::where(function ($query) use ($user_id, $my_id) {
            $query->where('from', $user_id)->where('to', $my_id);
        })->orWhere(function ($query) use ($user_id, $my_id) {
            $query->where('from', $my_id)->where('to', $user_id);
        })->get();
        $friend = User::find($user_id);
        return response()->json(['data' => $messages, 'friend'=>$friend]);
    }

    public function sendMessage(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'friend_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
            'message' => ['required']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

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
