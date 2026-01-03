<?php

namespace App\Http\Controllers;

use App\Domain\Models\Patient;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $patients = Patient::when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })->paginate(8)->withQueryString();

        return Inertia::render('tables/patient-table', [
            'patients' => $patients,
            'filters' => [
                'search' => $search,
            ],
            'userRole' => 'receptionist'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email',
            'cpf' => 'required|string|max:14|unique:patients,cpf',
            'phone' => 'required|string|max:20',
            'gender' => 'required|in:male,female,other',
            'birth_date' => 'required|date',
            'emergency_contact' => 'required|string|max:20',
            'medical_history' => 'nullable|string',
        ]);

        try {
            $patient = Patient::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'cpf' => $validated['cpf'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'],
                'birth_date' => $validated['birth_date'],
                'emergency_contact' => $validated['emergency_contact'],
                'medical_history' => $validated['medical_history'] ?? '',
            ]);

            return back()->with('success', 'Paciente criado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao criar paciente: ' . $e->getMessage()]);
        }
    }

    public function show(Patient $patient)
    {
        return response()->json([
            'id' => $patient->id,
            'name' => $patient->name,
            'email' => $patient->email,
            'cpf' => $patient->cpf,
            'phone' => $patient->phone,
            'gender' => $patient->gender,
            'birth_date' => $patient->birth_date,
            'emergency_contact' => $patient->emergency_contact,
            'medical_history' => $patient->medical_history,
        ]);
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:patients,email,' . $patient->id,
            'phone' => 'required|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact' => 'nullable|string|max:20',
            'medical_history' => 'nullable|string',
        ]);

        try {
            $patient->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'gender' => $validated['gender'] ?? $patient->gender,
                'emergency_contact' => $validated['emergency_contact'] ?? $patient->emergency_contact,
                'medical_history' => $validated['medical_history'] ?? $patient->medical_history,
            ]);

            return back()->with('success', 'Paciente atualizado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao atualizar paciente: ' . $e->getMessage()]);
        }
    }

    public function destroy(Patient $patient)
    {
        try {
            $patient->delete();
            
            return back()->with('success', 'Paciente deletado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao deletar paciente: ' . $e->getMessage()]);
        }
    }
}
