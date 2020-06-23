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

    public function socialLogin(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'email' => 'required|string|email',
                // 'remember_me' => 'boolean'
            ]);
        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }

        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            // $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
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
        } else {
            $user = new User([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'first_time_login' => 1,
                'active' => 1,
                'image' => "default_profile_picture.png",
                'cover_photo' => 'default_cover_photo.jpg',
                'activation_token' => 'ku',

            ]);

            $user->save();
            // $user1 = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
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

    }
    public function login(Request $request)
    {
        // return response()->json(['fulfillmentMessages' => array(array('card' => array("title"=>"Nothing special",'subtitle'=>'30 people going','buttons' => array(array('text' => "I want to go", "postback"=>"https://google.com")), 'imageUri' => 'data:image/jpeg;base64,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')))]);

        //   $object = json_decode(json_encode($array));

        // {
        //     "fulfillmentMessages": [
        //       {
        //         "text": {
        //           "text": [
        //             "Text response from webhook"
        //           ]
        //         }
        //       }
        //     ]
        //   }

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
        $token->save();
        // if ($request->remember_me)
        //     $token->expires_at = Carbon::now()->addWeeks(1);

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
