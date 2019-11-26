<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Topic;
use Faker\Generator as Faker;

$factory->define(Topic::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence,
        'user_id' => function() {
            return factory('App\User')->create()->id;
        },
    ];
});
