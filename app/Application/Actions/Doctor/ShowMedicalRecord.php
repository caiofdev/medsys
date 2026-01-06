<?php

namespace App\Application\Actions\Doctor;

use App\Domain\Contracts\DoctorRepositoryInterface;
use App\Domain\Models\Patient;

class ShowMedicalRecord
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepository
    ) {}

    /**
     * Show detailed patient medical record
     *
     * @param int $doctorId Doctor ID
     * @param int $patientId Patient ID
     * @return array
     */
     public function execute(int $doctorId, int $patientId): array
    {
        $doctor = $this->doctorRepository->findById($doctorId);
        
        $patient = Patient::with('user')->findOrFail($patientId);
        
        $consultations = $this->doctorRepository->getPatientConsultations($doctorId, $patientId)
            ->map(function ($consultation) {
                return [
                    'id' => $consultation->id,
                    'date' => $consultation->appointment->appointment_date,
                    'type' => 'Consulta Médica',
                    'diagnosis' => $consultation->diagnosis,
                    'symptoms' => $consultation->symptoms,
                    'notes' => $consultation->notes,
                ];
            });

        $medicalHistory = [
            'allergies' => [
                'Penicilina',
                'Ácido acetilsalicílico (AAS)',
                'Amendoim'
            ],
            'medications' => [
                'Losartana 50mg - 1x ao dia',
                'Metformina 500mg - 2x ao dia',
                'Omeprazol 20mg - 1x ao dia'
            ],
            'conditions' => [
                'Hipertensão arterial',
                'Diabetes tipo 2',
                'Gastrite crônica'
            ],
            'surgeries' => [
                'Apendicectomia (2018)',
                'Colecistectomia laparoscópica (2020)'
            ]
        ];

        return [
            'patient' => $patient,
            'consultations' => $consultations,
            'medicalHistory' => $medicalHistory,
        ];
    }
}