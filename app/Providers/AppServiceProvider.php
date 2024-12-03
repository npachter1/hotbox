<?php

namespace App\Providers;

use App\Models\Dispensary\Sale;
use App\Observers\SaleObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Resources\Json\Resource;
use Laravel\Passport\Passport;
use Laravel\Passport\Client;

use App\Helpers\Generator;
use Laravel\Telescope\Telescope;
use App\Providers\TelescopeServiceProvider;


class AppServiceProvider extends ServiceProvider{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(){

        Schema::defaultStringLength(191);                                       // to be compatable with MYSQL 5.6

        Client::creating(function (Client $client) {                            // update laravel-passport client primary to uuid
            $client->incrementing = false;
            $client->id = (isset($client->id) ? $client->id : Generator::uuid4());
        });
        Client::retrieved(function (Client $client) {
            $client->incrementing = false;
        });

        Passport::routes();                                                     // Register passport token registration routes
        Passport::tokensExpireIn(now()->addDays(3));                            // 3-day token expiration
        Passport::refreshTokensExpireIn(now()->addDays(720));                   // 2-year refresh
        Passport::cookie('web_access_token');                                   // web generated cookie access token - if needed

        Resource::withoutWrapping();                                            // dont wrap a data in json resources (unless a collection with paganition)

        Sale::observe(SaleObserver::class);                             //Observer the Sale class for HB financial
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(){

        Passport::ignoreMigrations();                                           // use our own migrations for uuidS
        Telescope::ignoreMigrations();
        if ($this->app->isLocal()) {
            //Telescope::night();
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

    }

}
