<?php

namespace App\Application\Actions\Doctor;

use App\Domain\Contracts\DoctorRepositoryInterface;
use App\Infrastructure\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class UpdateDoctor
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepository,
        private FileUploadService $fileUploadService
    ) {}

    /**
     * Execute doctor update
     *
     * @param int $doctorId Doctor ID
     * @param array $data Validated data from DoctorUpdateRequest 
     * @return void
     */
    public function execute(int $doctorId, array $data): void
    {
        DB::transaction(function () use ($doctorId, $data) {

            $doctor = $this->doctorRepository->findById($doctorId);

            if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
                if ($doctor->user->photo) {
                    $this->fileUploadService->delete($doctor->user->photo);
                }
                
                $data['photo'] = $this->fileUploadService->upload($data['photo'], 'photos');
            } else {
                unset($data['photo']);
            }

            $this->doctorRepository->update($doctor, $data);
        });
    }
}