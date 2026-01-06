<?php

namespace App\Application\Actions\Doctor;

use App\Domain\Contracts\DoctorRepositoryInterface;
use App\Domain\Models\Appointment;
use App\Domain\Models\Patient;

class StartConsultation
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepository
    ) {}

    /**
     * Get data to start a consultation
     *
     * @param int $doctorId Doctor ID
     * @return array
     */
    public function execute(int $doctorId): array
    {
        $doctor = $this->doctorRepository->findById($doctorId);

        $appointments = Appointment::where('doctor_id', $doctor->id)
            ->where('status', 'scheduled')
            ->whereDate('appointment_date', today())
            ->with(['patient.user'])
            ->orderBy('appointment_date', 'asc')
            ->get();

        $patients = Patient::with('user')->get();

        return [
            'appointments' => $appointments,
            'patients' => $patients,
        ];
    }
}