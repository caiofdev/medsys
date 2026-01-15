<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Actions\Doctor\CreateDoctor;
use App\Application\Actions\Doctor\UpdateDoctor;
use App\Application\Actions\Doctor\DeleteDoctor;
use App\Application\Actions\Doctor\SearchDoctor;
use App\Application\Actions\Doctor\ShowDoctor;
use App\Application\Actions\Doctor\StartConsultation;
use App\Application\Actions\Doctor\FinishConsultation;
use App\Application\Actions\Doctor\GetMedicalRecords;
use App\Application\Actions\Doctor\ShowMedicalRecord;
use App\Domain\Exceptions\DoctorNotFoundException;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Doctor\DoctorStoreRequest;
use App\Presentation\Http\Requests\Doctor\DoctorUpdateRequest;
use App\Presentation\Http\Requests\Doctor\FinishConsultationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DoctorController extends Controller
{
    public function __construct(
        private CreateDoctor $createDoctor,
        private UpdateDoctor $updateDoctor,
        private DeleteDoctor $deleteDoctor,
        private SearchDoctor $searchDoctor,
        private ShowDoctor $showDoctor,
        private StartConsultation $startConsultation,
        private FinishConsultation $finishConsultation,
        private GetMedicalRecords $getMedicalRecords,
        private ShowMedicalRecord $showMedicalRecord
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->input('search');
        
        $doctors = $this->searchDoctor->execute($search, 8);

        return Inertia::render('tables/doctor-table', [
            'doctors' => $doctors,
            'filters' => ['search' => $search],
            'userRole' => 'admin'
        ]);
    }

    public function store(DoctorStoreRequest $request): RedirectResponse
    {
        try {
            $this->createDoctor->execute($request->validated());

            return back()->with('success', 'Médico criado com sucesso.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao criar médico: ' . $e->getMessage()]);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $doctor = $this->showDoctor->execute($id);
            $doctor->load('user');

            return response()->json([
                'id' => $doctor->id,
                'name' => $doctor->user->name,
                'email' => $doctor->user->email,
                'cpf' => $doctor->user->cpf,
                'rg' => $doctor->user->rg,
                'phone' => $doctor->user->phone,
                'photo' => $doctor->user->photo ? asset('storage/' . $doctor->user->photo) : null,
                'crm' => $doctor->crm,
                'birth_date' => $doctor->user->birth_date,
            ]);
            
        } catch (DoctorNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function update(DoctorUpdateRequest $request, int $id): RedirectResponse
    {
        try {
            $this->updateDoctor->execute($id, $request->validated());

            return back()->with('success', 'Médico atualizado com sucesso.');
                
        } catch (DoctorNotFoundException $e) {
            return back()->withErrors(['message' => 'Médico não encontrado.']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao atualizar médico: ' . $e->getMessage()]);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->deleteDoctor->execute($id);

            return back()->with('success', 'Médico deletado com sucesso.');
                
        } catch (DoctorNotFoundException $e) {
            return back()->withErrors(['message' => 'Médico não encontrado.']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao deletar médico: ' . $e->getMessage()]);
        }
    }

    public function startConsultation(): Response
    {
        try {
            $user = auth()->user();
            $doctor = $user->doctor;

            if (!$doctor) {
                abort(403, 'Acesso negado. Usuário não é um médico.');
            }

            $data = $this->startConsultation->execute($doctor->id);

            return Inertia::render('doctors/start-consultation', [
                'appointments' => $data['appointments'],
                'patients' => $data['patients'],
                'userRole' => 'doctor',
            ]);

        } catch (\Exception $e) {
            abort(500, 'Erro ao carregar página de consultas: ' . $e->getMessage());
        }
    }

    public function finishConsultation(FinishConsultationRequest $request): RedirectResponse
    {
        try {
            $user = auth()->user();
            $doctor = $user->doctor;

            if (!$doctor) {
                return back()->withErrors(['message' => 'Acesso negado. Usuário não é um médico.']);
            }

            $this->finishConsultation->execute($doctor->id, $request->validated());

            return back()->with('success', 'Consulta finalizada com sucesso!');

        } catch (\Exception $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function medicalRecords(): Response
    {
        try {
            $user = auth()->user();
            $doctor = $user->doctor;

            if (!$doctor) {
                abort(403, 'Acesso negado. Usuário não é um médico.');
            }

            $data = $this->getMedicalRecords->execute($doctor->id);

            return Inertia::render('doctors/medical-record', [
                'patients' => $data['patients'],
                'doctor' => $data['doctor'],
                'consultationData' => $data['consultationData'],
                'userRole' => 'doctor',
            ]);

        } catch (\Exception $e) {
            abort(500, 'Erro ao carregar prontuários: ' . $e->getMessage());
        }
    }

    public function showMedicalRecord(int $patientId): Response
    {
        try {
            $user = auth()->user();
            $doctor = $user->doctor;

            if (!$doctor) {
                abort(403, 'Acesso negado. Usuário não é um médico.');
            }

            $data = $this->showMedicalRecord->execute($doctor->id, $patientId);

            return Inertia::render('doctors/individual-medical-record', [
                'patient' => $data['patient'],
                'consultations' => $data['consultations'],
                'medicalHistory' => $data['medicalHistory'],
                'userRole' => 'doctor'
            ]);

        } catch (\Exception $e) {
            abort(500, 'Erro ao carregar prontuário: ' . $e->getMessage());
        }
    }
}