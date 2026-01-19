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
        Model::preventLazyLoading(! app()->isProduction());
        
        Route::bind('doctor', function ($value) {
            return Doctor::with('user')->findOrFail($value);
        });

        Route::bind('receptionist', function ($value) {
            return Receptionist::with('user')->findOrFail($value);
        });
    }
}
