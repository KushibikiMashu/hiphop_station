<?php

use Faker\Generator as Faker;
use App\Video;

$factory->define(Video::class, function (Faker $faker) {
    $now = \Carbon\Carbon::now();
    $ids = \App\Channel::pluck('id');
    return [
        'channel_id' => $ids[array_rand(\App\Channel::pluck('id')->toArray())],
        'title' => $faker->realText(30),
        'hash' => str_random(11),
        'genre' => $faker->randomElement(['song', 'battle']),
        'published_at' => (string)$now,
        'created_at' => $now,
        'updated_at' => $now,
    ];
});
