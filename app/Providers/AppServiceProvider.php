<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
// TAMBAHKAN DUA BARIS INI
use App\Models\CourseMaterial;
use App\Policies\CourseMaterialPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // TAMBAHKAN REGISTRASI POLICY ANDA DI SINI
        CourseMaterial::class => CourseMaterialPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Panggil registerPolicies() jika belum ada
        $this->registerPolicies();
    }
}