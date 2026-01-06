<?php

namespace App\Application\Actions\Doctor;

use App\Domain\Contracts\DoctorRepositoryInterface;
use App\Domain\Models\Appointment;
use App\Domain\Models\Consultation;
use Illuminate\Support\Facades\DB;

class FinishConsultation
{
    public function __construct(
        private DoctorRepositoryInterface $doctorRepository
    ) {}

    /**
     * Finish a consultation
     *
     * @param int $doctorId Doctor ID
     * @param array $data Consultation data
     * @return void
     * @throws \Exception
     */
    public function execute(int $doctorId, array $data): void
    {
        DB::transaction(function () use ($doctorId, $data) {
            $doctor = $this->doctorRepository->findById($doctorId);

            $appointment = Appointment::where('id', $data['appointment_id'])
                ->where('doctor_id', $doctor->id)
                ->first();

            if (!$appointment) {
                throw new \Exception('Agendamento não encontrado ou não pertence a este médico.');
            }

            Consultation::create([
                'appointment_id' => $appointment->id,
                'symptoms' => $data['symptoms'],
                'diagnosis' => $data['diagnosis'],
                'notes' => $data['notes'] ?? '',
            ]);

            $appointment->update(['status' => 'completed']);
        });
    }
}