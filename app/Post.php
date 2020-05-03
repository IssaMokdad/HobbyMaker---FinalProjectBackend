<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Comments;
use App\Likes;
class Post extends Model
{
    protected $fillable = ['title','content','user_id', 'image',];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comments::class);
    }

    public function likes()
    {
        return $this->hasMany(Likes::class);
    }
}
