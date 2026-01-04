<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\Models\Doctor;
use App\Domain\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $doctors = Doctor::with(['user'])->when($search, function ($query) use ($search) {
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%");
            });
        })->paginate(8)->withQueryString();

        return Inertia::render('tables/doctor-table', [
            'doctors' => $doctors,
            'filters' => [
                'search' => $search,
            ],
            'userRole' => 'admin'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|string|max:14|unique:users,cpf',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'birth_date' => 'required|date',
            'crm' => 'required|string|max:20|unique:doctors,crm',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'cpf' => $validated['cpf'],
                'phone' => $validated['phone'],
                'password' => bcrypt($validated['password']),
                'birth_date' => $validated['birth_date'],
            ];

            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/photos'), $filename);
                $userData['photo'] = 'photos/' . $filename;
            }

            $user = User::create($userData);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'crm' => $validated['crm'],
            ]);

            return back()->with('success', 'Médico criado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao criar médico: ' . $e->getMessage()]);
        }
    }

    public function show(Doctor $doctor)
    {
        // Otimização: carregar user junto com doctor via route binding
        $doctor->loadMissing('user');
        
        return response()->json([
            'id' => $doctor->id,
            'name' => $doctor->user->name,
            'email' => $doctor->user->email,
            'cpf' => $doctor->user->cpf,
            'phone' => $doctor->user->phone,
            'photo' => $doctor->user->photo ? asset('storage/' . $doctor->user->photo) : null,
            'birth_date' => $doctor->user->birth_date,
            'crm' => $doctor->crm,
        ]);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->user->id,
            'phone' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if ($request->hasFile('photo')) {
            if ($doctor->user->photo && file_exists(public_path('storage/' . $doctor->user->photo))) {
                unlink(public_path('storage/' . $doctor->user->photo));
            }

            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/photos'), $filename);
            $updateData['photo'] = 'photos/' . $filename;
        }

        $doctor->user->update($updateData);

        return back()->with('success', 'Médico atualizado com sucesso.');
    }

    public function destroy(Doctor $doctor)
    {
        try {
            if ($doctor->user->photo && file_exists(public_path('storage/' . $doctor->user->photo))) {
                unlink(public_path('storage/' . $doctor->user->photo));
            }

            $doctor->user->delete();
            
            return back()->with('success', 'Médico deletado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao deletar médico: ' . $e->getMessage()]);
        }
    }

    public function startConsultation()
    {
        $user = auth()->user();
        $doctor = $user->doctor;
        
        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->withErrors(['message' => 'Acesso negado.']);
        }

        $appointments = \App\Domain\Models\Appointment::with(['patient'])
            ->where('doctor_id', $doctor->id)
            ->where('status', 'scheduled')
            ->whereDate('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date', 'asc')
            ->get();

        $patients = \App\Domain\Models\Patient::whereHas('appointments', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })
        ->select('id', 'name', 'email', 'cpf', 'phone', 'birth_date', 'gender', 'emergency_contact', 'medical_history')
        ->orderBy('name', 'asc')
        ->get();

        return Inertia::render('doctors/start-consultation', [
            'appointments' => $appointments,
            'patients' => $patients,
            'userRole' => 'doctor'
        ]);
    }

    public function finishConsultation(Request $request)
    {
        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'symptoms' => 'required|string',
            'diagnosis' => 'required|string',
            'notes' => 'nullable|string',
        ], [
            'appointment_id.required' => 'ID do agendamento é obrigatório.',
            'appointment_id.exists' => 'Agendamento não encontrado.',
            'symptoms.required' => 'Sintomas são obrigatórios.',
            'diagnosis.required' => 'Diagnóstico é obrigatório.',
        ]);

        try {
            $user = auth()->user();
            $doctor = $user->doctor;
            
            if (!$doctor) {
                return back()->withErrors(['message' => 'Acesso negado.']);
            }

            // Verificar se o appointment pertence ao médico logado
            $appointment = \App\Domain\Models\Appointment::where('id', $validated['appointment_id'])
                ->where('doctor_id', $doctor->id)
                ->first();

            if (!$appointment) {
                return back()->withErrors(['message' => 'Agendamento não encontrado ou não pertence a este médico.']);
            }

            // Criar a consulta
            $consultation = \App\Domain\Models\Consultation::create([
                'appointment_id' => $appointment->id,
                'symptoms' => $validated['symptoms'],
                'diagnosis' => $validated['diagnosis'],
                'notes' => $validated['notes'] ?? '',
            ]);

            // Atualizar o status do appointment para 'completed'
            $appointment->update(['status' => 'completed']);

            return back()->with('success', 'Consulta finalizada com sucesso!');

        } catch (\Exception $e) {
            \Log::error('Erro ao finalizar consulta', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            
            return back()->withErrors(['message' => 'Erro interno do servidor. Tente novamente.']);
        }
    }

    public function medicalRecords()
    {
        $user = auth()->user();
        $doctor = $user->doctor;
        
        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->withErrors(['message' => 'Acesso negado.']);
        }

        // Buscar pacientes que já tiveram consultas com este médico
        $patients = \App\Domain\Models\Patient::whereHas('appointments', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)
                  ->where('status', 'completed');
        })
        ->with(['appointments' => function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)
                  ->where('status', 'completed')
                  ->with('consultation');
        }])
        ->select('id', 'name', 'email', 'cpf', 'phone', 'birth_date', 'gender', 'emergency_contact', 'medical_history')
        ->orderBy('name', 'asc')
        ->get();

        // Buscar todas as consultas concluídas deste médico
        $consultationData = \App\Domain\Models\Consultation::whereHas('appointment', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)
                  ->where('status', 'completed');
        })
        ->with(['appointment.patient'])
        ->orderBy('created_at', 'desc')
        ->get();

        return Inertia::render('doctors/medical-record', [
            'patients' => $patients,
            'doctor' => $doctor->load('user'),
            'consultationData' => $consultationData,
            'userRole' => 'doctor'
        ]);
    }

    public function showMedicalRecord($patientId)
    {
        $user = auth()->user();
        $doctor = $user->doctor;
        
        if (!$doctor) {
            return redirect()->route('doctor.dashboard')->withErrors(['message' => 'Acesso negado.']);
        }

        // Verificar se o paciente teve consultas com este médico
        $patient = \App\Domain\Models\Patient::whereHas('appointments', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)
                  ->where('status', 'completed');
        })
        ->where('id', $patientId)
        ->first();

        if (!$patient) {
            return redirect()->route('doctor.medical-record')->withErrors(['message' => 'Paciente não encontrado ou sem consultas realizadas.']);
        }

        // Buscar todas as consultas deste paciente com este médico
        $consultations = \App\Domain\Models\Consultation::whereHas('appointment', function ($query) use ($doctor, $patientId) {
            $query->where('doctor_id', $doctor->id)
                  ->where('patient_id', $patientId)
                  ->where('status', 'completed');
        })
        ->with(['appointment'])
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($consultation) {
            return [
                'id' => $consultation->id,
                'date' => $consultation->appointment->appointment_date,
                'type' => 'Consulta Médica', // Pode ser expandido para diferentes tipos
                'diagnosis' => $consultation->diagnosis,
                'symptoms' => $consultation->symptoms,
                'notes' => $consultation->notes,
            ];
        });

        // Histórico médico simulado - pode ser expandido com dados reais
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

        return Inertia::render('doctors/individual-medical-record', [
            'patient' => $patient,
            'consultations' => $consultations,
            'medicalHistory' => $medicalHistory,
            'userRole' => 'doctor'
        ]);
    }
}
