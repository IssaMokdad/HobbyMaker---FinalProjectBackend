<?php

namespace App\Http\Controllers;

use App\Friend;
use DB;
use Illuminate\Http\Request;
use Validator;

class FriendController extends Controller
{

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'friend_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
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

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
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

        $validator = Validator::make($request->all(), [
            'friend_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
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

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $users = DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.friend_id')
            ->where('friends.status', 'request')
            ->where('friends.user_id', $request->input('user_id'))
            ->get();
        return response()->json(['data' => $users]);
    }

    public function getPendingRequests(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
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
        $validator = Validator::make($request->all(), [
            'friend_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
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
