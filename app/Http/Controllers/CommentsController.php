<?php

namespace App\Http\Controllers;

use App\Comments;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentsController extends Controller
{
    public function add(Request $request)
    {
        // var_dump(json_decode($request->json()->all()));

        // $data = $request->json()->all();
        $validator = Validator::make($request->all(), [
            'comment' => ['required', 'string', 'max:255'],
            'post_id' => ['required', 'integer', 'min:1'],
            'user_id' => ['required', 'integer', 'min:1']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
        $comment = Comments::create([
            'comment' => $request->input('comment'),
            'post_id' => $request->input('post_id'),
            'user_id' => $request->input('user_id'),
        ]);

        $comment = $comment->fresh();
        // $datas=[];
        // array_push($datas, $data, ['id'=>Auth::id()]);
        $commentTotal = Post::find($request->input('post_id'));
        $commentTotal = $commentTotal->comments->count();
        return response()->json(['comment' => $comment, 'commentTotal' => $commentTotal]);
        // return response()->json(['foo'=>'bar']);
    }
}
