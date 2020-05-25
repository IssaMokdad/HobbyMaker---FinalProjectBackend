<?php

namespace App\Http\Controllers;

use App\Events\AddRequestEvent;
use App\Friend;
use App\Notifications\AddRequest;
use App\User;
use DB;
use Illuminate\Http\Request;

class FriendController extends Controller
{

    public function add(Request $request)
    {

        $validate = new Friend;

        $validate->validateFriendRequest($request);

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
        $validate->validateUserRequest($request);

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
        $validate->validateFriendRequest($request);

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
        $validate->validateUserRequest($request);

        $users = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.friend_id')
            ->where('friends.status', 'request')
            ->where('friends.user_id', $request->input('user_id'))
            ->get();
        return response()->json(['data' => $users]);
    }

    public function getPendingRequests(Request $request)
    {

        $validate = new User;
        $validate->validateUserRequest($request);

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
        $validate->validateFriendRequest($request);

        $friend1 = Friend::where('user_id', $request->input('user_id'))
            ->where('friend_id', $request->input('friend_id'))
            ->update(['status' => 'accepted']);
        $friend2 = Friend::where('user_id', $request->input('friend_id'))
            ->where('friend_id', $request->input('user_id'))
            ->update(['status' => 'accepted']);
        if ($friend1 && $friend2) {
            return response()->json(['message' => 'success']);} else {
            return response()->json(['data' => 'error']);
        }
    }
}
