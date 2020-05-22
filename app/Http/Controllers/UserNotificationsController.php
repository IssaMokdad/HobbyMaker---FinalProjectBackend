<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;

class UserNotificationsController extends Controller
{
    public function showNotifications(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $notifications = User::find($request->input('user_id'))->unreadNotifications;

        return response()->json(['data' => $notifications]);
    }

    public function markNotificationsAsRead(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $notifications = User::find($request->input('user_id'))->unreadNotifications;
        $notifications->markAsRead();

        if($notifications){
            return response()->json(['message' => 'success']);
        }
        else{
            return response()->json(['message' => 'error']);
        }

    }
}
