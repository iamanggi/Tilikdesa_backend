<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Routing\Route;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Tambahkan registrasi service jika diperlukan
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Gunakan Tailwind untuk pagination
        Paginator::useTailwind();

        // Definisikan Gate untuk admin
        Gate::define('admin', function ($user) {
            return $user->is_admin == true;
        });

        // Konfigurasi Scramble untuk dokumentasi hanya pada route 'api/*'
        Scramble::configure()->routes(function (Route $route) {
            return Str::startsWith($route->uri(), 'api/');
        })->withDocumentTransformers(function(OpenApi $openApi){
            $openApi -> secure(
                SecurityScheme::http('bearer')
            );
        });
    }
}