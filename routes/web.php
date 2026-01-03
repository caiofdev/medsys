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
        
    Route::get('receptionists/{receptionist}', [ReceptionistController::class, 'show'])->name('receptionist.show')->middleware('user.type:admin');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('doctor/dashboard', [DashboardController::class, 'doctorDashboard'])->name('doctor.dashboard')->middleware('user.type:doctor');
    Route::get('receptionist/dashboard', [DashboardController::class, 'receptionistDashboard'])->name('receptionist.dashboard')->middleware('user.type:receptionist');

    Route::get('receptionist/patients', [PatientController::class, 'index'])->name('patient.table')->middleware('user.type:receptionist');
    
    

    Route::get('receptionist/patients/{patient}', [PatientController::class, 'show'])->name('patient.show')->middleware('user.type:receptionist');
    Route::post('receptionist/patients', [PatientController::class, 'store'])->name('patient.store')->middleware('user.type:receptionist');
    Route::put('receptionist/patients/{patient}', [PatientController::class, 'update'])->name('patient.update')->middleware('user.type:receptionist');
    Route::delete('receptionist/patients/{patient}', [PatientController::class, 'destroy'])->name('patient.destroy')->middleware('user.type:receptionist');

    Route::get('doctor/start-consultation', [DoctorController::class, 'startConsultation'])->name('doctor.start-consultation')->middleware('user.type:doctor');
    Route::post('doctor/finish-consultation', [DoctorController::class, 'finishConsultation'])->name('doctor.finish-consultation')->middleware('user.type:doctor');
    Route::get('doctor/medical-record', [DoctorController::class, 'medicalRecords'])->name('doctor.medical-record')->middleware('user.type:doctor');
    Route::get('doctor/medical-record/{patient}', [DoctorController::class, 'showMedicalRecord'])->name('doctor.medical-record.show')->middleware('user.type:doctor');

    Route::post('receptionist/appointments', [AppointmentController::class, 'store'])->name('appointment.store')->middleware('user.type:receptionist');
    Route::get('receptionist/appointments/patients', [AppointmentController::class, 'getPatients'])->name('appointment.patients')->middleware('user.type:receptionist');
    Route::get('receptionist/appointments/doctors', [AppointmentController::class, 'getDoctors'])->name('appointment.doctors')->middleware('user.type:receptionist');

    Route::get('receptionist/consultations-list', [DashboardController::class, 'consultationsList'])->name('receptionist.consultations-list')->middleware('user.type:receptionist');

    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->middleware('web');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';