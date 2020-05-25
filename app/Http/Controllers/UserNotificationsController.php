<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserNotificationsController extends Controller
{
    public function showNotifications(Request $request)
    {

        $validate = new User;
        $validate->validateUserRequest($request);

        $notifications = User::find($request->input('user_id'))->unreadNotifications;

        return response()->json(['data' => $notifications]);
    }

    public function markNotificationsAsRead(Request $request)
    {

        $validate = new User;

        $validate->validateUserRequest($request);

        $notifications = User::find($request->input('user_id'))->unreadNotifications;
        $notifications->markAsRead();

        if ($notifications) {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }

    }
}
