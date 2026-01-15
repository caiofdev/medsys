<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Actions\Patient\CreatePatient;
use App\Application\Actions\Patient\UpdatePatient;
use App\Application\Actions\Patient\DeletePatient;
use App\Application\Actions\Patient\SearchPatient;
use App\Application\Actions\Patient\ShowPatient;
use App\Domain\Exceptions\PatientNotFoundException;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Patient\PatientStoreRequest;
use App\Presentation\Http\Requests\Patient\PatientUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
    public function __construct(
        private CreatePatient $createPatient,
        private UpdatePatient $updatePatient,
        private DeletePatient $deletePatient,
        private SearchPatient $searchPatient,
        private ShowPatient $showPatient
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->input('search');
        
        $patients = $this->searchPatient->execute($search, 8);

        return Inertia::render('tables/patient-table', [
            'patients' => $patients,
            'filters' => ['search' => $search],
            'userRole' => 'receptionist'
        ]);
    }

    public function store(PatientStoreRequest $request): RedirectResponse
    {
        try {
            $this->createPatient->execute($request->validated());

            return back()->with('success', 'Paciente criado com sucesso.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao criar paciente: ' . $e->getMessage()]);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $patient = $this->showPatient->execute($id);

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
            
        } catch (PatientNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function update(PatientUpdateRequest $request, int $id): RedirectResponse
    {
        try {
            $this->updatePatient->execute($id, $request->validated());

            return back()->with('success', 'Paciente atualizado com sucesso.');
                
        } catch (PatientNotFoundException $e) {
            return back()->withErrors(['message' => 'Paciente não encontrado.']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao atualizar paciente: ' . $e->getMessage()]);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->deletePatient->execute($id);

            return back()->with('success', 'Paciente deletado com sucesso.');
                
        } catch (PatientNotFoundException $e) {
            return back()->withErrors(['message' => 'Paciente não encontrado.']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao deletar paciente: ' . $e->getMessage()]);
        }
    }
}