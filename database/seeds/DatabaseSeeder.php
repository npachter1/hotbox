<?php

use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{

    const SEED_USER_ID = '491a9970-c966-4345-9361-bcac9b2db695';
    const SEED_API_USER_ID = 'e9f9ff2c-e00b-4760-ac22-18727a1d0f58';
    const SEED_LOCATION_ID = '123456';



    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        \Artisan::call('passport:install');                                     // install the personal and password app access clients for the oauth

        
        switch(config('app.env')){
            case 'local':
                $this->call(AuthSeeder::class);
                break;
            case 'demo':
                $this->call(AuthSeederDemo::class);
                
                break;
            case 'production':
                $this->call(AuthSeederProduction::class);
                break;
            default: $this->call(AuthSeeder::class); // local
        }

    }
}
