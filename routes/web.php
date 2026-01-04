<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReceptionistController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('user.type:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            
        Route::get('dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

        Route::apiResource('admins', AdminController::class);

        Route::apiResource('doctors', DoctorController::class);

        Route::apiResource('receptionists', ReceptionistController::class);

        Route::controller(DashboardController::class)->prefix('reports')->name('reports.')->group(function () {
            Route::get('last-consultations', 'getLastFiveCompletedConsultationsApi')->name('last-consultations');
            Route::get('monthly-revenue', 'getMonthlyRevenueApi')->name('monthly-revenue');
        });

    });
            
    Route::middleware('user.type:receptionist')
        ->prefix('receptionist')
        ->name('receptionist.')
        ->group(function () {

        Route::get('dashboard', [DashboardController::class, 'receptionistDashboard'])->name('dashboard');
        Route::get('consultations-list', [DashboardController::class, 'consultationsList'])->name('consultations-list');

        Route::apiResource('patients', PatientController::class);

        Route::controller(AppointmentController::class)->prefix('appointments')->name('appointment.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::get('patients', 'getPatients')->name('patients');
            Route::get('doctors', 'getDoctors')->name('doctors');
        });  
              
    });

    Route::middleware('user.type:doctor')
        ->prefix('doctor')
        ->name('doctor.')
        ->group(function () {

        Route::get('dashboard', [DashboardController::class, 'doctorDashboard'])->name('dashboard');
        Route::get('medical-record/{patient}', [DoctorController::class, 'showMedicalRecord'])->name('medical-record.show');
        Route::get('medical-record', [DoctorController::class, 'medicalRecords'])->name('medical-record');

        Route::get('start-consultation', [DoctorController::class, 'startConsultation'])->name('start-consultation');
        Route::post('finish-consultation', [DoctorController::class, 'finishConsultation'])->name('finish-consultation');
    });
    
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';