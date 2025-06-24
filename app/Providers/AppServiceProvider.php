<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\CourseMaterial;
use App\Policies\CourseMaterialPolicy;


class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        CourseMaterial::class => CourseMaterialPolicy::class, 
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
