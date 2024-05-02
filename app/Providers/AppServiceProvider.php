<?php

namespace App\Providers;


/*
|--------------------------------------------------------------------------
| Illuminate Namespace
|--------------------------------------------------------------------------
*/
use Illuminate\Support\ServiceProvider;

/*
|--------------------------------------------------------------------------
| Contracts Namespace
|--------------------------------------------------------------------------
*/
use App\Contracts\CardContract;
use App\Contracts\FirestoreContract;
use App\Contracts\BigQueryProviderContract;
use App\Contracts\PushNotificationContract;

/*
|--------------------------------------------------------------------------
| Services  Namespace
|--------------------------------------------------------------------------
*/
use App\Services\Mocks\BigQueryProviderMock;
use App\Services\Providers\BigQueryProvider;
use App\Services\Providers\FirestoreProvider;
use App\Services\Providers\PaystackServiceProvider;
use App\Services\Providers\FirebaseNotificationProvider;
use App\Services\Providers\FirestoreProviderMock;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // if ($this->app->environment('local')) {
        //     $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        //     $this->app->register(TelescopeServiceProvider::class);
        // }
    }

    /*
    |--------------------------------------------------------------------------
    | add comment
    |--------------------------------------------------------------------------
    */
    public function boot()
    {
        $this->bindServiceProviders();
    }

    /*
    |--------------------------------------------------------------------------
    | add comment
    |--------------------------------------------------------------------------
    */
    private function bindServiceProviders()
    {
        if ($this->app->environment('production') || $this->app->environment('staging') || $this->app->environment('development')) {
            $this->bindProductionServiceProviders();
        } elseif ($this->app->environment('local') || $this->app->environment('testing')) {
            $this->bindLocalServiceProviders();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Production, Development, Staging Providers
    |--------------------------------------------------------------------------
    */
    private function bindProductionServiceProviders()
    {
        $this->app->bind(FirestoreContract::class, FirestoreProvider::class);
        $this->app->bind(CardContract::class, PaystackServiceProvider::class);
        $this->app->bind(BigQueryProviderContract::class, BigQueryProvider::class);
        $this->app->bind(PushNotificationContract::class, FirebaseNotificationProvider::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Local & Testing Providers
    |--------------------------------------------------------------------------
    */
    private function bindLocalServiceProviders()
    {
        $this->app->bind(CardContract::class, PaystackServiceProvider::class);
        $this->app->bind(FirestoreContract::class, FirestoreProviderMock::class);
        $this->app->bind(BigQueryProviderContract::class, BigQueryProviderMock::class);
        $this->app->bind(PushNotificationContract::class, FirebaseNotificationProvider::class);
    }
}
