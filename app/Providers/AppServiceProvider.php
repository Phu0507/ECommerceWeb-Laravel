<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $serviceName = [
        'App\Services\Interfaces\UserServiceInterface'=> 'App\Services\UserService',
        'App\Services\Interfaces\ProvinceServiceInterface' => 'App\Services\ProvinceService',
        'App\Repositories\Interfaces\UserRepositoryInterface' => 'App\Repositories\UserRepository',
        'App\Services\Interfaces\DistrictServiceInterface' => 'App\Services\DistrictService',
        'App\Services\Interfaces\WardServiceInterface' => 'App\Services\WardService',
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        foreach ($this->serviceName as $interface => $service) {
            $this->app->bind($interface, $service);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}