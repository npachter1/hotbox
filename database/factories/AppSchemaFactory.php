<?php

use App\Models\AppSchema;
use Faker\Generator as Faker;


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

$factory->define(AppSchema::class, function (Faker $faker) {

    return [
        'code'              => null,
        'type'              => 'model',
        'content'           => null
    ];
    
});
