<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Comments;
use Faker\Generator as Faker;

$factory->define(Comments::class, function (Faker $faker) {
    return [
        "user_id"=>factory(App\User::class)->create(),
        "post_id"=>factory(App\Post::class)->create(),
        "comment"=>$faker->paragraph,
    ];
});
