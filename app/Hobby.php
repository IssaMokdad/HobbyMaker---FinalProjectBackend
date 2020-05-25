<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hobby extends Model
{

    protected $fillable = ['hobby', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
