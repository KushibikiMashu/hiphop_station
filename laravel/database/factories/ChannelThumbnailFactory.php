<?php

use Faker\Generator as Faker;
use App\ChannelThumbnail;

$factory->define(ChannelThumbnail::class, function (Faker $faker) {
    $now = \Carbon\Carbon::now();
    return [
        'std' => $faker->url,
        'medium' => $faker->url,
        'high' => $faker->url,
        'created_at' => $now,
        'updated_at' => $now,
    ];
});
