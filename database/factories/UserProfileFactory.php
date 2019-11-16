<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\UserProfile::class, function (Faker $faker) {
    return [
        'bio' => $faker->paragraph,
    ];
});
