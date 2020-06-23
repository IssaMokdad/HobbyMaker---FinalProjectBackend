<?php

namespace App\Http\Controllers;

use App\Events\AddRequestEvent;
use App\Events\AcceptFriendRequestEvent;
use App\Friend;
use Validator;
use App\Notifications\AddRequest;
use App\Notifications\AcceptFriendRequest;
use App\User;
use DB;
use Illuminate\Http\Request;

class FriendController extends Controller
{

    public function add(Request $request)
    {

        $validate = new Friend;

        $error = $validate->validateFriendRequest($request);;
        if($error){
            return $error;
        }
       
        $userSendToRequest = User::find($request->input('friend_id'));

        $userThatSentRequest = User::find($request->input('user_id'));

        $userSendToRequest->notify(new AddRequest($userThatSentRequest));

        event(new AddRequestEvent($userSendToRequest, $userThatSentRequest));

        $friend1 = Friend::create([
            'friend_id' => $request->input('friend_id'),
            'user_id' => $request->input('user_id'),
            'status' => 'pending',
        ]);
        $friend2 = Friend::create([
            'user_id' => $request->input('friend_id'),
            'friend_id' => $request->input('user_id'),
            'status' => 'request',
        ]);
        if ($friend1 && $friend2) {
            return response()->json(['message' => 'success']);} else {
            return response()->json(['data' => 'error']);
        }
    }

    public function getFriends(Request $request)
    {

        $validate = new User;
        $error = $validate->validateUserRequest($request);
        if($error){
            return $error;
        }
        $users = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.friend_id')
            ->where('friends.status', 'accepted')
            ->where('friends.user_id', $request->input('user_id'))
            ->get();
        return response()->json(['data' => $users]);
    }

    public function removeFriend(Request $request)
    {

        $validate = new Friend;

        $error = $validate->validateFriendRequest($request);;
        if($error){
            return $error;
        }

        $friend1 = Friend::where('user_id', $request->input('user_id'))
            ->where('friend_id', $request->input('friend_id'))
            ->delete();

        $friend2 = Friend::where('user_id', $request->input('friend_id'))
            ->where('user_id', $request->input('friend_id'))
            ->delete();

        if ($friend1 && $friend2) {
            return response()->json(['message' => 'success']);} else {
            return response()->json(['data' => 'error']);
        }

    }

    public function getFriendRequests(Request $request)
    {

        $validate = new User;
        $error = $validate->validateUserRequest($request);
        if($error){
            return $error;
        }

        $users = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.friend_id')
            ->where('friends.status', 'request')
            ->where('friends.user_id', $request->input('user_id'))
            ->get();
        return response()->json(['data' => $users]);
    }
    public function search(Request $request){

        $validator = Validator::make($request->all(), [
            'search_value' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
       }
       
        $results = User::where('first_name', 'like', '%' . $request->input('search_value') . '%')
                        ->orWhere('last_name', 'like', '%' . $request->input('search_value') . '%')->get();
                        return response()->json(['data' => $results]);
    }
    public function getPendingRequests(Request $request)
    {

        $validate = new User;
        $error = $validate->validateUserRequest($request);
        if($error){
            return $error;
        }

        $users = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.friend_id')
            ->where('friends.status', 'pending')
            ->where('friends.user_id', $request->input('user_id'))
            ->get();
        return response()->json(['data' => $users]);
    }

    public function accept(Request $request)
    {

        $validate = new Friend;

        $error = $validate->validateFriendRequest($request);;
        if($error){
            return $error;
        }

        $friend1 = Friend::where('user_id', $request->input('user_id'))
            ->where('friend_id', $request->input('friend_id'))
            ->update(['status' => 'accepted']);
        $friend2 = Friend::where('user_id', $request->input('friend_id'))
            ->where('friend_id', $request->input('user_id'))
            ->update(['status' => 'accepted']);
        if ($friend1 && $friend2) {
            $userThatSentRequest = User::find($request->input('friend_id'));

            $userSendToRequest = User::find($request->input('user_id'));

            event(new AcceptFriendRequestEvent($userThatSentRequest, $userSendToRequest));
            
            $userThatSentRequest->notify(new AcceptFriendRequest($userSendToRequest));

            

            return response()->json(['message' => 'success']);} else {
            return response()->json(['data' => 'error']);
        }
    }
}
