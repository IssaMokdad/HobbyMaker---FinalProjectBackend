<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UserNotificationsController extends Controller
{
    public function showNotifications(Request $request){

        $notifications = User::find($request->input('user_id'))->unreadNotifications;

        return response()->json(['data' => $notifications]);
    }

    public function markNotificationsAsRead(Request $request){


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
