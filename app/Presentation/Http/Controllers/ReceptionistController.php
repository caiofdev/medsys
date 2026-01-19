<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Actions\Receptionist\CreateReceptionist;
use App\Application\Actions\Receptionist\UpdateReceptionist;
use App\Application\Actions\Receptionist\DeleteReceptionist;
use App\Application\Actions\Receptionist\SearchReceptionist;
use App\Application\Actions\Receptionist\ShowReceptionist;
use App\Domain\Exceptions\ReceptionistNotFoundException;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Receptionist\ReceptionistStoreRequest;
use App\Presentation\Http\Requests\Receptionist\ReceptionistUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReceptionistController extends Controller
{
    public function __construct(
        private CreateReceptionist $createReceptionist,
        private UpdateReceptionist $updateReceptionist,
        private DeleteReceptionist $deleteReceptionist,
        private SearchReceptionist $searchReceptionist,
        private ShowReceptionist $showReceptionist
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->input('search');
        
        $receptionists = $this->searchReceptionist->execute($search, 8);

        return Inertia::render('receptionists/receptionist-table', [
            'receptionists' => $receptionists,
            'filters' => ['search' => $search],
            'userRole' => 'admin'
        ]);
    }

    public function store(ReceptionistStoreRequest $request): RedirectResponse
    {
        try {
            $this->createReceptionist->execute($request->validated());

            return back()->with('success', 'Recepcionista criado com sucesso.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao criar recepcionista: ' . $e->getMessage()]);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $receptionist = $this->showReceptionist->execute($id);

            return response()->json([
                'id' => $receptionist->id,
                'name' => $receptionist->user->name,
                'email' => $receptionist->user->email,
                'cpf' => $receptionist->user->cpf,
                'phone' => $receptionist->user->phone,
                'photo' => $receptionist->user->photo ? asset('storage/' . $receptionist->user->photo) : null,
                'birth_date' => $receptionist->user->birth_date,
                'register_number' => $receptionist->registration_number,
            ]);
            
        } catch (ReceptionistNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function update(ReceptionistUpdateRequest $request, int $id): RedirectResponse
    {
        try {
            $this->updateReceptionist->execute($id, $request->validated());

            return back()->with('success', 'Recepcionista atualizado com sucesso.');
                
        } catch (ReceptionistNotFoundException $e) {
            return back()->withErrors(['message' => 'Recepcionista não encontrado.']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao atualizar recepcionista: ' . $e->getMessage()]);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->deleteReceptionist->execute($id);

            return back()->with('success', 'Recepcionista deletado com sucesso.');
                
        } catch (ReceptionistNotFoundException $e) {
            return back()->withErrors(['message' => 'Recepcionista não encontrado.']);
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao deletar recepcionista: ' . $e->getMessage()]);
        }
    }
}