<?php

namespace App\Application\Actions\Doctor;

use App\Domain\Contracts\DoctorRepositoryInterface;

class GetMedicalRecords
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepository
    ) {}

    /**
     * Get list of patients with medical records
     *
     * @param int $doctorId Doctor ID
     * @return array
     */
    public function execute(int $doctorId): array
    {
        $doctor = $this->doctorRepository->findById($doctorId);
        
        $patients = $this->doctorRepository->getPatientsWithConsultations($doctorId);
        
        $consultationData = [];
        foreach ($patients as $item) { 
            if (!$item->patient) {
                continue; 
            }

            $consultations = $this->doctorRepository->getPatientConsultations($doctorId, $item->patient->id);
            foreach ($consultations as $consultation) {
                $consultationData[] = [
                    'id' => $consultation->id,
                    'appointment' => $consultation->appointment,
                    'symptoms' => $consultation->symptoms,
                    'diagnosis' => $consultation->diagnosis,
                    'notes' => $consultation->notes,
                ];
            }
        }

        return [
            'patients' => $patients,
            'doctor' => $doctor,
            'consultationData' => $consultationData,
        ];
    }
}