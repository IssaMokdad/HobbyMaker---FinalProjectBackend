<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\SignupActivate;
use App\Notifications\SignupActivated;
use App\User;
use Avatar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Storage;
use Str;
use Validator;

class AuthController extends Controller
{
    /**
     * Create user deactivate and send notification to activate account user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'gender' => 'required|in:male,female',
                'email' => 'required|string|email|unique:users',
                'birthday' => 'required|date',
                'password' => 'required|string|min:8',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $user = new User([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'password' => bcrypt($request->password),
            'first_time_login' => 1,
            'activation_token' => Str::random(60),
        ]);

        $user->save();

        $avatar = Avatar::create($user->first_name)->getImageObject()->encode('png');
        Storage::put('avatars/' . $user->id . '/avatar.png', (string) $avatar);

        $user->notify(new SignupActivate($user));

        return response()->json([
            'message' => __('auth.signup_success'),
        ], 201);
    }

    /**
     * Confirm your account user (Activate)
     *
     * @param  [type] $token
     * @return [string] message
     * @return [obj] user
     */
    public function signupActivate($token)
    {

        $user = User::where('activation_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => __('auth.token_invalid'),
            ], 404);
        }

        $user->active = true;
        $user->activation_token = '';
        $user->image = 'default_profile_picture.png';
        $user->cover_photo = 'default_cover_photo.jpg';
        $user->save();

        $user->notify(new SignupActivated($user));

        return $user;
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'email' => 'required|string|email',
                'password' => 'required|string',
                // 'remember_me' => 'boolean'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $credentials = request(['email', 'password']);
        $credentials['active'] = 1;
        $credentials['deleted_at'] = null;

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => __('auth.login_failed'),
            ], 401);
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;

        // if ($request->remember_me)
        //     $token->expires_at = Carbon::now()->addWeeks(1);

        $token->save();

        return response()->json([
            'user_id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'message' => __('auth.login_success'),
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'first_time_login' => $user->first_time_login,
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => __('auth.logout_success'),
        ]);
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
