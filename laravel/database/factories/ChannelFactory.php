<?php

use Faker\Generator as Faker;
use App\Channel;

$factory->define(Channel::class, function (Faker $faker) {
    $now = \Carbon\Carbon::now();
    return [
        'title' => $faker->realText(20),
        'hash' => str_random(24),
        'video_count' => $faker->randomDigit(3),
        'published_at' => (string)$now,
        'created_at' => $now,
        'updated_at' => $now,
    ];
});
