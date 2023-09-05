<?php

namespace App\Providers;

use App\Services\Common\CustomizePaginate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Illuminate\Pagination\LengthAwarePaginator', function ($app, $options) {
            return new CustomizePaginate($options['items'], $options['total'], $options['perPage'], $options['currentPage'], $options['options']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }
}
