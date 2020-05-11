<?php

namespace App\Http\Controllers;
use App\User;
use App\Hobby;
use Illuminate\Http\Request;
use DB;
use Image;
use Validator;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    public function getUsersWithSameHobbyAndAddress(Request $request){
        //get the user
        //get the friends ids and add the authenticated user id to the array
        //get the users having common hobby who are at the same country and city
        //exlude the friends ids and the authenticated user id from the result
        $user = User::find($request->input('user_id'));
        
        $friendsIds = array_column($user->friend->toArray(), 'friend_id');

        $userHobbies = array_column($user->hobby->toArray(), 'hobby');
        $friendsIds[] = $request->input('user_id');

        $users = DB::table('users')
        ->join('hobbies', 'users.id', '=', 'hobbies.user_id')
        ->where('users.country', $user->country)
        ->where('users.city',$user->city)
        ->whereIn('hobbies.hobby', $userHobbies)
        ->whereNotIn('users.id', $friendsIds )
        ->select('users.id','users.birthday','hobbies.hobby','users.first_name','users.last_name', 'users.image')
        ->get();
        return response()->json(['data'=>$users]);
    }

    public function getUserInfo(Request $request){
        return new UserResource(User::find($request->input('user-id')));
    }

    public function saveProfilePicture(Request $request){


            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'user_id' => ['required', 'integer', 'min:1'],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->messages(), 419);
            }

            $filename = date('Y-m-d-H-i-s').'userid='.$request->input('user_id').'.'.$request->file('image')->getClientOriginalExtension();
            Image::make($request->file('image')->getRealPath())->resize(150, 150)->save(public_path('images/'.$filename));

            $user = User::where('id', $request->input('user_id'))
                ->update(['image' => $filename]);
            if ($user) {
                return response()->json(['message' => 'success']);
            } else {
                return response()->json(['message' => 'error']);
            }
        
    }

    public function saveCoverPicture(Request $request){


        $validator = Validator::make($request->all(), [
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'user_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }

        $filename = date('Y-m-d-H-i-s').'userid='.$request->input('user_id').'.'.$request->file('cover_photo')->getClientOriginalExtension();
        Image::make($request->file('cover_photo')->getRealPath())->resize(1158, 250)->save(public_path('images/'.$filename));

        $user = User::where('id', $request->input('user_id'))
            ->update(['cover_photo' => $filename]);
        if ($user) {
            return response()->json(['message' => 'success']);
        } else {
            return response()->json(['message' => 'error']);
        }
    
}

    public function changePassword(Request $request){
        $validator  =   Validator::make($request->all(),
        [
            'password' => 'required|string|confirmed',
            'user_id' => ['required', 'integer', 'min:1'],
        ]
    );

    if($validator->fails()) {
        return response()->json(['Validation errors' => $validator->errors()]);
    }
    $password = bcrypt($request->input('password'));
    $user = User::find($request->input('user_id'));
    $user->password = $password;
    $user->save;
    if($user->isDirty('password')){

        return response()->json(['message' => 'success']);
    } else {
        return response()->json(['message' => 'error']);
    }

    }


    public function editUserInfo(Request $request)
    {



            $validator = Validator::make($request->all(), [
                'birthday' => ['required','date'],
                'country' => ['required', 'string', 'max:50' ],
                'city' => ['required', 'string', 'max:50' ],
                'first_name' => ['required', 'string', 'max:50' ],
                'last_name' => ['required', 'string', 'max:50' ],
                'user_id' => ['required', 'integer', 'min:1'],
                'hobby' => ['required', 'string', ],
            ]);

            if ($validator->fails()) {
                return response()->json($validator->messages(), 419);
            }

            $user = User::find($request->input('user_id'));
            
            if(sizeof($user->hobby)!==0){
                $hobby = Hobby::where('user_id',$request->input('user_id'))
            ->update(['hobby'=>$request->input('hobby')]);
            }
            else{
                $hobby = Hobby::create([
                    'user_id' => $request->input('user_id'),
                    'hobby' => $request->input('hobby'),
                ]);
            }
            $user = User::where('id', $request->input('user_id'))
                ->update(['birthday' => $request->input('birthday'),'city' => $request->input('city'),'country' => $request->input('country'),'first_name' => $request->input('first_name'), 'last_name' => $request->input('last_name')]);
            
            // $hobby = Hobby::where('user_id',$request->input('user_id'))
            // ->update(['hobby'=>$request->input('hobby')]);
            
            if ($user && $hobby) {
                return response()->json(['message' => 'success']);
            } else {
                return response()->json(['message' => 'error']);
            }
        

    }


}