<?php

use App\Models\Auth\User;
use Faker\Generator as Faker;

use App\Helpers\Generator;


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    static $password;

    return [
        'id'               => Generator::uuid4(),
        'email'            => $faker->unique()->safeEmail,
        'password'         => $password ?: $password = bcrypt('secret'),
        'remember_token'   => str_random(10),
        'activation_token' => str_random(10),
        'status'           => 'activated',
    ];
});
