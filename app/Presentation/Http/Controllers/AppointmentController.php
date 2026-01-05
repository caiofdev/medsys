<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\Models\Appointment;
use App\Domain\Models\Doctor;
use App\Domain\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Inertia\Inertia;

class AppointmentController extends Controller
{
    public function index()
    {
        
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'price' => 'required|numeric|min:0',
            'status' => 'required|string|in:scheduled,canceled,completed'
        ], [
            'patient_id.required' => 'Selecione um paciente',
            'patient_id.exists' => 'Paciente não encontrado',
            'doctor_id.required' => 'Selecione um médico',
            'doctor_id.exists' => 'Médico não encontrado',
            'date.required' => 'Data é obrigatória',
            'date.date' => 'Data deve ser uma data válida',
            'date.after_or_equal' => 'Data deve ser hoje ou uma data futura',
            'time.required' => 'Horário é obrigatório',
            'time.date_format' => 'Horário deve estar no formato HH:MM',
            'price.required' => 'Valor é obrigatório',
            'price.numeric' => 'Valor deve ser um número',
            'price.min' => 'Valor deve ser maior que zero'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        try {
            $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);

            $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
                ->where('appointment_date', $appointmentDateTime)
                ->where('status', '!=', 'canceled')
                ->first();

            if ($existingAppointment) {
                return back()->withErrors(['message' => 'Este médico já possui uma consulta agendada para este horário']);
            }

            $patientConflict = Appointment::where('patient_id', $request->patient_id)
                ->where('appointment_date', $appointmentDateTime)
                ->where('status', '!=', 'canceled')
                ->first();

            if ($patientConflict) {
                return back()->withErrors(['message' => 'Este paciente já possui uma consulta agendada para este horário']);
            }

            $user = Auth::user();
            $receptionistId = $user->receptionist ? $user->receptionist->id : null;

            $appointment = Appointment::create([
                'patient_id' => $request->patient_id,
                'doctor_id' => $request->doctor_id,
                'appointment_date' => $appointmentDateTime,
                'value' => $request->price,
                'status' => 'scheduled',
                'receptionist_id' => $receptionistId
            ]);

            return back()->with('success', 'Consulta agendada com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao criar consulta', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return back()->withErrors(['message' => 'Erro interno do servidor. Tente novamente.']);
        }
    }

    public function getPatients(Request $request)
    {
        $query = $request->get('q', '');
        
        $patients = Patient::when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->where('name', 'LIKE', "%{$query}%")
                                    ->orWhere('cpf', 'LIKE', "%{$query}%")
                                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'cpf', 'email', 'phone')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'patients' => $patients
        ]);
    }

    public function getDoctors(Request $request)
    {
        $query = $request->get('q', '');
        
        $doctors = Doctor::with('user:id,name,email')
            ->when($query, function ($queryBuilder) use ($query) {
                return $queryBuilder->whereHas('user', function ($userQuery) use ($query) {
                    $userQuery->where('name', 'LIKE', "%{$query}%")
                            ->orWhere('email', 'LIKE', "%{$query}%");
                })->orWhere('crm', 'LIKE', "%{$query}%");
            })
            ->select('id', 'crm', 'user_id')
            ->limit(10)
            ->get()
            ->map(function ($doctor) {
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name,
                    'email' => $doctor->user->email,
                    'crm' => $doctor->crm
                ];
            });

        return response()->json([
            'success' => true,
            'doctors' => $doctors
        ]);
    }
}
