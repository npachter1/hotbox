<?php

use App\Models\Auth\Addressbook;
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

$factory->define(Addressbook::class, function (Faker $faker) {

    return [
        'type'              => 'misc',
        'name'              => 'John and Jane Doe',
        'address1'          => '123 Main St.',
        'city'              => 'Anywhere',
        'region'            => 'CO',
        'country'           => 'US',
        'zip'               => '80210',
        'phone'             => '555-555-5555',
        'contact_notes'     => 'Ask for John',
        'email'             => 'johnjanedoe@gmail.com'
    ];
});
