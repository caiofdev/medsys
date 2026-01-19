<?php

namespace App\Providers;

use App\Application\Actions\Admin\CreateAdmin;
use App\Application\Actions\Admin\UpdateAdmin;
use App\Application\Actions\Admin\DeleteAdmin;
use App\Application\Actions\Admin\SearchAdmin;
use App\Application\Actions\Admin\ShowAdmin;
use App\Application\Actions\Doctor\CreateDoctor;
use App\Application\Actions\Doctor\UpdateDoctor;
use App\Application\Actions\Doctor\DeleteDoctor;
use App\Application\Actions\Doctor\SearchDoctor;
use App\Application\Actions\Doctor\ShowDoctor;
use App\Application\Actions\Doctor\StartConsultation;
use App\Application\Actions\Doctor\FinishConsultation;
use App\Application\Actions\Doctor\GetMedicalRecords;
use App\Application\Actions\Doctor\ShowMedicalRecord;
use App\Application\Actions\Patient\CreatePatient;
use App\Application\Actions\Patient\UpdatePatient;
use App\Application\Actions\Patient\DeletePatient;
use App\Application\Actions\Patient\SearchPatient;
use App\Application\Actions\Patient\ShowPatient;
use App\Application\Actions\Patient\SearchPatientForAutocomplete;
use App\Application\Actions\Receptionist\CreateReceptionist;
use App\Application\Actions\Receptionist\UpdateReceptionist;
use App\Application\Actions\Receptionist\DeleteReceptionist;
use App\Application\Actions\Receptionist\SearchReceptionist;
use App\Application\Actions\Receptionist\ShowReceptionist;
use App\Application\Actions\Dashboard\GetAdminDashboardData;
use App\Application\Actions\Dashboard\GetDoctorDashboardData;
use App\Application\Actions\Dashboard\GetReceptionistDashboardData;
use App\Application\Actions\Dashboard\GetConsultationsList;
use App\Domain\Contracts\AdminRepositoryInterface;
use App\Domain\Contracts\DoctorRepositoryInterface;
use App\Domain\Contracts\PatientRepositoryInterface;
use App\Domain\Contracts\ReceptionistRepositoryInterface;
use App\Domain\Contracts\DashboardRepositoryInterface; 
use App\Infrastructure\Repositories\AdminRepository;
use App\Infrastructure\Repositories\DoctorRepository;
use App\Infrastructure\Repositories\PatientRepository;
use App\Infrastructure\Repositories\ReceptionistRepository;
use App\Infrastructure\Repositories\DashboardRepository; 
use App\Infrastructure\Services\DashboardStatisticsService;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AdminRepositoryInterface::class, AdminRepository::class);
        $this->app->bind(DoctorRepositoryInterface::class, DoctorRepository::class);
        $this->app->bind(PatientRepositoryInterface::class, PatientRepository::class);
        $this->app->bind(ReceptionistRepositoryInterface::class, ReceptionistRepository::class);
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class); 

        $this->app->singleton(FileUploadService::class);
        $this->app->singleton(DashboardStatisticsService::class);

        $this->app->bind(CreateAdmin::class);
        $this->app->bind(UpdateAdmin::class);
        $this->app->bind(DeleteAdmin::class);
        $this->app->bind(SearchAdmin::class);
        $this->app->bind(ShowAdmin::class);
        
        $this->app->bind(CreateDoctor::class);
        $this->app->bind(UpdateDoctor::class);
        $this->app->bind(DeleteDoctor::class);
        $this->app->bind(SearchDoctor::class);
        $this->app->bind(ShowDoctor::class);
        $this->app->bind(StartConsultation::class);
        $this->app->bind(FinishConsultation::class);
        $this->app->bind(GetMedicalRecords::class);
        $this->app->bind(ShowMedicalRecord::class);
        
        $this->app->bind(CreatePatient::class);
        $this->app->bind(UpdatePatient::class);
        $this->app->bind(DeletePatient::class);
        $this->app->bind(SearchPatient::class);
        $this->app->bind(ShowPatient::class);
        $this->app->bind(SearchPatientForAutocomplete::class);
        
        $this->app->bind(CreateReceptionist::class);
        $this->app->bind(UpdateReceptionist::class);
        $this->app->bind(DeleteReceptionist::class);
        $this->app->bind(SearchReceptionist::class);
        $this->app->bind(ShowReceptionist::class);
        
        $this->app->bind(GetAdminDashboardData::class);
        $this->app->bind(GetDoctorDashboardData::class);
        $this->app->bind(GetReceptionistDashboardData::class);
        $this->app->bind(GetConsultationsList::class);
    }

    public function boot(): void
    {
        //
    }
}