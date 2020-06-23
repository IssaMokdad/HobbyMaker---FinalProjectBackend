<?php

namespace App;

use App\Comments;
use App\Friend;
use App\Events;
use App\Hobby;
use App\Likes;
use App\Post;
use App\SavedPost;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Validator;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    protected $dates = ['deleted_at'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','image','cover_photo','first_time_login','bio', 'country', 'city', 'last_name', 'active', 'gender', 'birthday', 'email', 'password', 'activation_token', 'avatar', 'longitude', 'latitude',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'token', 'active', 'remember_token', 'activation_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function comments()
    {
        return $this->hasMany(Comments::class);
    }
    public function post()
    {
        return $this->hasMany(Post::class);
    }
    public function friend()
    {
        return $this->hasMany(Friend::class);
    }
    public function likes()
    {
        return $this->hasMany(Likes::class);
    }
    public function hobby()
    {
        return $this->hasMany(Hobby::class);
    }

    public function video()
    {
        return $this->hasMany(YoutubeVideos::class);
    }

    public function savedpost()
    {
        return $this->hasMany(SavedPost::class);
    }

    public function events()
    {
        return $this->hasMany(Events::class);
    }

    public function validateUserRequest($request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', 'min:1'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 419);
        }
    }

    public function goings()
    {
        return $this->hasMany(Going::class);
    }
}
