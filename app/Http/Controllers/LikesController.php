<?php

namespace App\Http\Controllers;

use App\Http\Resources\Post as PostResource;
use App\Likes;
use App\Post;
use Illuminate\Http\Request;

class LikesController extends Controller
{
    public function add(Request $request)
    {

        $validate = new Likes;
        $validate->validateLikeRequest($request);

        $like = Likes::create([
            'post_id' => $request->input('post_id'),
            'user_id' => $request->input('user_id'),
        ]);

        return new PostResource(Post::find($request->input('post_id')));
    }

    public function remove(Request $request)
    {

        $validate = new Likes;
        $validate->validateLikeRequest($request);

        Likes::where('user_id', $request->input('user_id'))
            ->where('post_id', $request->input('post_id'))
            ->delete();
        return new PostResource(Post::find($request->input('post_id')));

    }
}
