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

Route::middleware('user.type:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
        
            Route::get('dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

            Route::controller(AdminController::class)->prefix('admins')->name('admins.')->group(function () {
                Route::get('/', 'index')->name('index'); 
                Route::post('/', 'store')->name('store');
                Route::get('/{admin}', 'show')->name('show');
                Route::put('/{admin}', 'update')->name('update');
                Route::delete('/{admin}', 'destroy')->name('destroy');
            });

            Route::controller(DoctorController::class)->prefix('doctors')->name('doctors.')->group(function () {
                Route::get('/', 'index')->name('index');      
                Route::post('/', 'store')->name('store');
                Route::get('/{doctor}', 'show')->name('show');
                Route::put('/{doctor}', 'update')->name('update');
                Route::delete('/{doctor}', 'destroy')->name('destroy');
            });

            Route::controller(ReceptionistController::class)->prefix('receptionists')->name('receptionists.')->group(function () {
                Route::get('/', 'index')->name('index');      
                Route::post('/', 'store')->name('store');
                Route::get('/{receptionist}', 'show')->name('show');
                Route::put('/{receptionist}', 'update')->name('update');
                Route::delete('/{receptionist}', 'destroy')->name('destroy');
            });

            Route::controller(DashboardController::class)->prefix('reports')->name('reports.')->group(function () {
                Route::get('last-consultations', 'getLastFiveCompletedConsultationsApi')->name('last-consultations');
                Route::get('monthly-revenue', 'getMonthlyRevenueApi')->name('monthly-revenue');
            });
        });
        
Route::middleware('user.type:receptionist')
    ->prefix('receptionist')
    ->name('receptionist.')
    ->group(function () {
        Route::controller(PatientController::class)->prefix('patients')->name('patient.')->group(function () {
        Route::get('{patient}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::put('{patient}', 'update')->name('update');
        Route::delete('{patient}', 'destroy')->name('destroy');
        Route::get('/', 'index')->name('table');
        });

        Route::controller(AppointmentController::class)->prefix('appointments')->name('appointment.')->group(function () {
        Route::post('/', 'store')->name('store');
        Route::get('patients', 'getPatients')->name('patients');
        Route::get('doctors', 'getDoctors')->name('doctors');
        });
        
        Route::get('consultations-list', [DashboardController::class, 'consultationsList'])->name('consultations-list');
        Route::get('dashboard', [DashboardController::class, 'receptionistDashboard'])->name('dashboard');
    });


    Route::get('doctor/start-consultation', [DoctorController::class, 'startConsultation'])->name('doctor.start-consultation')->middleware('user.type:doctor');
    Route::post('doctor/finish-consultation', [DoctorController::class, 'finishConsultation'])->name('doctor.finish-consultation')->middleware('user.type:doctor');
    Route::get('doctor/medical-record', [DoctorController::class, 'medicalRecords'])->name('doctor.medical-record')->middleware('user.type:doctor');
    Route::get('doctor/medical-record/{patient}', [DoctorController::class, 'showMedicalRecord'])->name('doctor.medical-record.show')->middleware('user.type:doctor');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('doctor/dashboard', [DashboardController::class, 'doctorDashboard'])->name('doctor.dashboard')->middleware('user.type:doctor');
    
    


    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->middleware('web');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';