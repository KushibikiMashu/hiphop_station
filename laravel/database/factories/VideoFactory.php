<?php

use Faker\Generator as Faker;
use App\Video;

$factory->define(Video::class, function (Faker $faker) {
    $now = \Carbon\Carbon::now();
    return [
        'channel_id' => $faker->randomNumber(3),
        'title' => $faker->realText(30),
        'hash' => str_random(11),
        'genre' => $faker->randomElement(['song', 'battle']),
        'published_at' => (string)$now,
        'created_at' => $now,
        'updated_at' => $now,
    ];
});
