<?php

namespace App\Presentation\Http\Controllers;

use App\Application\Actions\Admin\CreateAdmin;
use App\Application\Actions\Admin\UpdateAdmin;
use App\Application\Actions\Admin\DeleteAdmin;
use App\Application\Actions\Admin\SearchAdmin;
use App\Application\Actions\Admin\ShowAdmin;
use App\Domain\Exceptions\AdminNotFoundException;
use App\Domain\Exceptions\CannotDeleteLastMasterException;
use App\Domain\Exceptions\CannotRemoveLastMasterException;
use App\Presentation\Http\Controllers\Controller;
use App\Presentation\Http\Requests\Admin\AdminStoreRequest;
use App\Presentation\Http\Requests\Admin\AdminUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function __construct(
        private CreateAdmin $createAdmin,
        private UpdateAdmin $updateAdmin,
        private DeleteAdmin $deleteAdmin,
        private SearchAdmin $searchAdmin,
        private ShowAdmin $showAdmin
    ) {}

    public function index(Request $request): Response
    {
        $search = $request->input('search');
        
        $admins = $this->searchAdmin->execute($search, 10);

        return Inertia::render('admins/admin-table', [
            'admins' => $admins,
            'filters' => ['search' => $search],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admins/create');
    }

    public function store(AdminStoreRequest $request): RedirectResponse
    {
        if ($request->input('is_master') === 'yes' && !auth()->user()->admin->is_master) {
            abort(403, 'Apenas administradores Master podem criar outros Masters.');
        }

        try {
            $this->createAdmin->execute($request->validated());

            return back()->with('success', 'Administrador criado com sucesso.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao criar administrador: ' . $e->getMessage()]);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $admin = $this->showAdmin->execute($id);

            return response()->json([
                'id' => $admin->id,
                'name' => $admin->user->name,
                'email' => $admin->user->email,
                'cpf' => $admin->user->cpf,
                'rg' => $admin->user->rg,
                'phone' => $admin->user->phone,
                'photo' => $admin->user->photo ? asset('storage/' . $admin->user->photo) : null,
                'is_master' => $admin->is_master,
                'birth_date' => $admin->user->birth_date,
            ]);
            
        } catch (AdminNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function edit(int $id): Response
    {
        try {
            $admin = $this->showAdmin->execute($id);

            return Inertia::render('admins/edit', [
                'admin' => $admin,
            ]);
            
        } catch (AdminNotFoundException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function update(AdminUpdateRequest $request, int $id): RedirectResponse
    {
        try {
            $this->updateAdmin->execute($id, $request->validated());

            return back()->with('success', 'Administrador atualizado com sucesso.');
                
        } catch (AdminNotFoundException $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
            
        } catch (CannotRemoveLastMasterException $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao atualizar administrador: ' . $e->getMessage()]);
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->deleteAdmin->execute($id);

            return back()->with('success', 'Administrador deletado com sucesso.');
                
        } catch (CannotDeleteLastMasterException $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
            
        } catch (AdminNotFoundException $e) {
            return back()->withErrors(['message' => $e->getMessage()]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao deletar administrador: ' . $e->getMessage()]);
        }
    }
}