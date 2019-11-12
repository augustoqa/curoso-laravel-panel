<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\UserProfile::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'bio' => $faker->paragraph,
    ];
});
