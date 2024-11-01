<?php

namespace App\Providers;

use App\Http\View\Composers\NotificationComposer;
use App\Models\PaymentMethod;
use App\Models\PaymentMethodExpigate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('dm_payment_method_authorize', function ($app) {
            return PaymentMethod::where('id', '!=', 5)->get();
        });

        $this->app->singleton('dm_payment_method_expigate', function ($app) {
            return PaymentMethodExpigate::get();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFive();
        // Paginator::useBootstrapFour();
        view()->composer('*', NotificationComposer::class);
        view()->share('privateKey','SASA');
    }
}
