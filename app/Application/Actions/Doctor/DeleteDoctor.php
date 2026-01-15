<?php

namespace App\Application\Actions\Doctor;

use App\Domain\Contracts\DoctorRepositoryInterface;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Support\Facades\DB;

class DeleteDoctor
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute doctor deletion
     *
     * @param int $doctorId Administrator ID
     * @return void
     */
    public function execute(int $doctorId): void
    {
        DB::transaction(function () use ($doctorId) {

            $doctor = $this->doctorRepository->findById($doctorId);

            if ($doctor->user->photo) {
                $this->fileUploadService->delete($doctor->user->photo);
            }

            $this->doctorRepository->delete($doctor);
        });
    }
}