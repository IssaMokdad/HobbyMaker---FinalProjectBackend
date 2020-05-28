<?php

namespace App;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $fillable = [
        'user_id','name','image', 'description', 'start_date', 'end_date', 'start_time', 'end_time', 'location'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goings()
    {
        return $this->hasMany(Going::class, 'event_id');
        
    }
}
