<?php

namespace App\Http\Controllers;

use App\Hobby;
use App\Http\Resources\User as UserResource;
use App\User;
use DB;
use Illuminate\Http\Request;
use Image;
use Validator;

class UserController extends Controller
{

    public function getUsersWithSameHobbyAndAddress(Request $request)
    {

        $validate = new User;

        $error = $validate->validateUserRequest($request);
        if($error){
            return $error;
        }
        $user = User::find($request->input('user_id'));

        $friendsIds = array_column($user->friend->toArray(), 'friend_id');

        $userHobbies = array_column($user->hobby->toArray(), 'hobby');

        $friendsIds[] = $request->input('user_id');

        $users = DB::table('users')
            ->join('hobbies', 'users.id', '=', 'hobbies.user_id')
            ->where('users.country', $user->country)
            ->where('users.city', $user->city)
            ->whereIn('hobbies.hobby', $userHobbies)
            ->whereNotIn('users.id', $friendsIds)
            ->select('users.id', 'users.birthday', 'users.longitude', 'users.latitude', 'hobbies.hobby', 'users.first_name', 'users.last_name', 'users.image')
            ->get();

        return response()->json(['data' => $users]);
    }

    public function getUserInfo(Request $request)
    {
        $validate = new User;

        $error = $validate->validateUserRequest($request);
        if($error){
            return $error;
        }

        return new UserResource(User::find($request->input('user_id')));
    }

    public function setFirstTimeLoginToFalse(Request $request)
    {
        $validate = new User;

        $error = $validate->validateUserRequest($request);
        if($error){
            return $error;
        }

        $user = User::find($request->input('user_id'));
        $user->first_time_login = 0;
        $user->save();
        return response()->json(['message' => 'success']);
    }

    public function saveProfilePicture(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $filename = date('Y-m-d-H-i-s') . 'userid=' . $request->input('user_id') . '.' . $request->file('image')->getClientOriginalExtension();
        Image::make($request->file('image')->getRealPath())->resize(150, 150)->save(public_path('images/' . $filename));

        $user = User::where('id', $request->input('user_id'))
            ->update(['image' => $filename]);
        if ($user) {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }

    }

    public function saveCoverPicture(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $filename = date('Y-m-d-H-i-s') . 'userid=' . $request->input('user_id') . '.' . $request->file('cover_photo')->getClientOriginalExtension();
        Image::make($request->file('cover_photo')->getRealPath())->resize(1158, 250)->save(public_path('images/' . $filename));

        $user = User::where('id', $request->input('user_id'))
            ->update(['cover_photo' => $filename]);
        if ($user) {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }

    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'password' => 'required|string|confirmed',
                'user_id' => ['required', 'integer', 'min:1'],
            ]
        );

        if ($validator->fails()) {
            return response()->json(['Validation errors' => $validator->errors()]);
        }
        $password = bcrypt($request->input('password'));
        $user = User::find($request->input('user_id'));
        $user->password = $password;
        $user->save;
        if ($user->isDirty('password')) {

            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }

    }

    public function editUserInfo(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'birthday' => ['required', 'date'],
            'country' => ['required', 'string', 'max:50'],
            'city' => ['required', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'user_id' => ['required', 'integer', 'min:1'],
            'hobby' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $user = User::find($request->input('user_id'));
        $hobbies = $request->input('hobby');
        $length = count($hobbies);

        Hobby::where('user_id', $request->input('user_id'))
            ->delete();

        for ($i = 0; $i < $length; $i++) {
            $hobby = Hobby::create([
                'user_id' => $request->input('user_id'),
                'hobby' => $hobbies[$i],
            ]);
        }

        $user = User::where('id', $request->input('user_id'))
            ->update(['birthday' => $request->input('birthday'), 'city' => $request->input('city'), 'country' => $request->input('country'), 'first_name' => $request->input('first_name'), 'last_name' => $request->input('last_name')]);

        if ($user && $hobby) {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }

    }

    public function saveGeometryPosition(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'longitude' => ['required'],
            'latitude' => ['required'],
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $user = User::where('id', $request->input('user_id'))
            ->update(['longitude' => $request->input('longitude'), 'latitude' => $request->input('latitude')]);

        if ($user) {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }

    }

}
