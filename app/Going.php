<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Going extends Model
{
    protected $fillable = ['status', 'user_id', 'event_id'];



public function event()
{
    return $this->belongsTo(Events::class);
}


public function user()
{
    return $this->belongsTo(User::class);
}

}