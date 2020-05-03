<?php

namespace App\Http\Controllers;
use App\Post;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use App\User;
use App\Http\Resources\Post as PostResource;
class PostController extends Controller
{

    public function get(Request $request){

        $friendsIds = array_column(User::find(1)->friend->toArray(), 'friend_id');

        return PostResource::collection(Post::orderBy('id', 'desc')->whereIn('user_id',$friendsIds)->take(10)->get());

    }
    public function add(Request $request){


        if ($request->hasFile('image')) {
            $request->validate([
                'title' => ['required', 'max:255'],
                'content' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'user_id'=>['required', 'integer']
            ]);
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . "." . $extension;
            $file->move('images/', $filename);
            $post = Post::create([
                'user_id' => $request->input('user_id'),
                'content' => $request->input('content'),
                'title' => $request->input('title'),
                'image'=>$filename,
            ]);
            return response()->json(['post'=>$post]);}
            else{
                $request->validate([
                    'title' => ['required', 'max:255'],
                    'content' => 'required',
                ]);
                $post = Post::create([
                    'user_id' => $request->input('user_id'),
                    'content' => $request->input('content'),
                    'title' => $request->input('title'),
                ]);
                return response()->json(['post'=>$post]);
            }
        }

    public function edit(Request $request){
        if ($request->hasFile('image')){
                $request->validate([
                    'title' => ['required', 'max:255'],
                    'content' => 'required',
                    'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                ]);
                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . "." . $extension;
                $file->move('images/', $filename);
            $post = Post::where('id',$request->input('id') )->where('user_id', $request->input('user_id'))
            ->update(['title' => $request->input('title'), 'content'=>$request->input('content'), 'image'=>$filename]);
            if($post){
                return response()->json(['post'=>$post]);
            }
             else{
                return response()->json(['message'=>'error']);
             }
        }
        else{
            $request->validate([
                'title' => ['required', 'max:255'],
                'content' => 'required',
                'id'      => 'required',
            ]);
            $post = Post::where('id',$request->input('id') )->where('user_id', $request->input('user_id'))
            ->update(['title' => $request->input('title'), 'content'=>$request->input('content')]);
            if($post){
                return response()->json(['post'=>$post]);
            }
             else{
                return response()->json(['message'=>'error']);
             }
        }



    }

    public function delete(Request $request){
        $request->validate([
            'id' => ['required'],
        ]);
        Post::where('id',$request->input('id'))
        ->delete();
        return redirect(url('home'));
    }
}
