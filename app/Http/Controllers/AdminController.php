<?php

namespace App\Http\Controllers;

use App\Domain\Models\Admin;
use App\Domain\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $admins = Admin::with('user')->when($search, function ($query) use ($search) {
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%");
            });
        })->paginate(8)->withQueryString();

        return Inertia::render('tables/admin-table', [
            'admins' => $admins,
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
            'is_master' => 'required|in:yes,no',
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

            $admin = Admin::create([
                'user_id' => $user->id,
                'is_master' => $validated['is_master'] === 'yes',
            ]);

            return back()->with('success', 'Administrador criado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao criar administrador: ' . $e->getMessage()]);
        }
    }

    public function show(Admin $admin)
    {
        $admin->load('user');
        
        return response()->json([
            'id' => $admin->id,
            'name' => $admin->user->name,
            'email' => $admin->user->email,
            'cpf' => $admin->user->cpf,
            'phone' => $admin->user->phone,
            'photo' => $admin->user->photo ? asset('storage/' . $admin->user->photo) : null,
            'is_master' => $admin->is_master,
            'birth_date' => $admin->user->birth_date,
        ]);
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->user->id,
            'phone' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if ($request->hasFile('photo')) {
            if ($admin->user->photo && file_exists(public_path('storage/' . $admin->user->photo))) {
                unlink(public_path('storage/' . $admin->user->photo));
            }

            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/photos'), $filename);
            $updateData['photo'] = 'photos/' . $filename;
        }

        $admin->user->update($updateData);

        return back()->with('success', 'Administrador atualizado com sucesso.');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->is_master) {
            $masterCount = Admin::where('is_master', true)->count();
            if ($masterCount <= 1) {
                return back()->withErrors(['message' => 'Não é possível deletar o último administrador master do sistema.']);
            }
        }

        try {
            $admin->user->delete();
            
            return back()->with('success', 'Administrador deletado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao deletar administrador: ' . $e->getMessage()]);
        }
    }
}
