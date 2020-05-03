<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Friend;
use App\User;
use Faker\Generator as Faker;

$factory->define(Friend::class, function (Faker $faker) {
    return [
        "user_id"=>1,
        "friend_id"=>factory(User::class)->create(),
    ];
});
