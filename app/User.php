<?php

namespace App;
use App\Post;
use App\Comments;
use App\Likes;
use App\Friend;
use App\Hobby;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'first_name', 'last_name','active', 'gender','birthday', 'email', 'password','activation_token', 'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'activation_token'
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
}
