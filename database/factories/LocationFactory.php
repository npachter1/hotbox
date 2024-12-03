<?php

use App\Models\Auth\Location;
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

$factory->define(Location::class, function (Faker $faker) {

    return [
        'id'                => Generator::uniqueLocation(),
        'name'              => 'New Location on '.date('m/d/y'),
        'is_demo'           => 1
    ];
    
});
