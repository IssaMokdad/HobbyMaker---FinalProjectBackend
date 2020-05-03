<?php

namespace App;
use App\Likes;
use App\User;
use App\Post;
use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $fillable = ['comment', 'user_id', 'post_id'];
    public function user()
    {
    return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
