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
    Route::get('admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard')->middleware('user.type:admin');
    Route::get('doctor/dashboard', [DashboardController::class, 'doctorDashboard'])->name('doctor.dashboard')->middleware('user.type:doctor');
    Route::get('receptionist/dashboard', [DashboardController::class, 'receptionistDashboard'])->name('receptionist.dashboard')->middleware('user.type:receptionist');

    Route::get('admin/admins', [AdminController::class, 'index'])->name('admin.table')->middleware('user.type:admin');
    Route::get('admin/doctors', [DoctorController::class, 'index'])->name('doctor.table')->middleware('user.type:admin');
    Route::get('admin/receptionists', [ReceptionistController::class, 'index'])->name('receptionist.table')->middleware('user.type:admin');
    Route::get('receptionist/patients', [PatientController::class, 'index'])->name('patient.table')->middleware('user.type:receptionist');
    
    Route::get('admin/admins/{admin}', [AdminController::class, 'show'])->name('admin.show')->middleware('user.type:admin');
    Route::post('admin/admins', [AdminController::class, 'store'])->name('admin.store')->middleware('user.type:admin');
    Route::put('admin/admins/{admin}', [AdminController::class, 'update'])->name('admin.update')->middleware('user.type:admin');
    Route::delete('admin/admins/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy')->middleware('user.type:admin');
    
    Route::get('admin/reports/last-consultations', [DashboardController::class, 'getLastFiveCompletedConsultationsApi'])->name('admin.reports.last-consultations')->middleware('user.type:admin');
    Route::get('admin/reports/monthly-revenue', [DashboardController::class, 'getMonthlyRevenueApi'])->name('admin.reports.monthly-revenue')->middleware('user.type:admin');

    Route::get('admin/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctor.show')->middleware('user.type:admin');
    Route::post('admin/doctors', [DoctorController::class, 'store'])->name('doctor.store')->middleware('user.type:admin');
    Route::put('admin/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctor.update')->middleware('user.type:admin');
    Route::delete('admin/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctor.destroy')->middleware('user.type:admin');

    Route::get('admin/receptionists/{receptionist}', [ReceptionistController::class, 'show'])->name('receptionist.show')->middleware('user.type:admin');
    Route::post('admin/receptionists', [ReceptionistController::class, 'store'])->name('receptionist.store')->middleware('user.type:admin');
    Route::put('admin/receptionists/{receptionist}', [ReceptionistController::class, 'update'])->name('receptionist.update')->middleware('user.type:admin');
    Route::delete('admin/receptionists/{receptionist}', [ReceptionistController::class, 'destroy'])->name('receptionist.destroy')->middleware('user.type:admin');

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
    
    Route::get('calendar', function () {
        return Inertia::render('Calendar');
    })->name('calendar');

    Route::get('calendar/appointments', [AppointmentController::class, 'getCalendarAppointments'])->name('calendar.appointments');


    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    })->middleware('web');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';