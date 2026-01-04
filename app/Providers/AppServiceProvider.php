<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use App\Domain\Models\Doctor;
use App\Domain\Models\Receptionist;
use App\Domain\Models\Appointment;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // OTIMIZAÇÃO CRÍTICA: Prevenir lazy loading em produção
        // Isso força a detectar N+1 problems durante desenvolvimento
        Model::preventLazyLoading(! app()->isProduction());
        
        // OTIMIZAÇÃO: Eager load automático via Route::bind
        // Carrega relações automaticamente para evitar N+1 queries
        Route::bind('doctor', function ($value) {
            return Doctor::with('user')->findOrFail($value);
        });

        Route::bind('receptionist', function ($value) {
            return Receptionist::with('user')->findOrFail($value);
        });
    }
}
