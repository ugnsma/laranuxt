<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Post;
use Faker\Generator as Faker;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'body' => $faker->sentence,
        'topic_id' => function() {
            return factory('App\Topic')->create()->id;
        },
        'user_id' => function() {
            return factory('App\User')->create()->id;
        }
    ];
});
