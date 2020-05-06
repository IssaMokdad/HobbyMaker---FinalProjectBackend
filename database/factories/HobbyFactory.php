<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Hobby;
use Faker\Generator as Faker;
use App\User;
$factory->define(Hobby::class, function (Faker $faker) {
    return [
        "user_id"=>factory(User::class)->create(),
        "hobby"=>'Cycling',
    ];
});
