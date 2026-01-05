<?php

namespace App\Presentation\Http\Controllers;

use App\Domain\Models\Receptionist;
use App\Domain\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReceptionistController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        
        $receptionists = Receptionist::with('user')->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });
            })->paginate(8)->withQueryString();

        return Inertia::render('tables/receptionist-table', [
            'receptionists' => $receptionists,
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
            'register_number' => 'required|string|max:20|unique:receptionists,registration_number',
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

            $receptionist = Receptionist::create([
                'user_id' => $user->id,
                'registration_number' => $validated['register_number'],
            ]);

            return back()->with('success', 'Recepcionista criado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao criar recepcionista: ' . $e->getMessage()]);
        }
    }

    public function show(Receptionist $receptionist)
    {
        $receptionist->loadMissing('user');
        
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
    }

    public function update(Request $request, Receptionist $receptionist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $receptionist->user->id,
            'phone' => 'required|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ];

        if ($request->hasFile('photo')) {
            if ($receptionist->user->photo && file_exists(public_path('storage/' . $receptionist->user->photo))) {
                unlink(public_path('storage/' . $receptionist->user->photo));
            }

            $file = $request->file('photo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/photos'), $filename);
            $updateData['photo'] = 'photos/' . $filename;
        }

        $receptionist->user->update($updateData);

        return back()->with('success', 'Recepcionista atualizado com sucesso.');
    }

    public function destroy(Receptionist $receptionist)
    {
        try {
            if ($receptionist->user->photo && file_exists(public_path('storage/' . $receptionist->user->photo))) {
                unlink(public_path('storage/' . $receptionist->user->photo));
            }

            $receptionist->user->delete();
            
            return back()->with('success', 'Recepcionista deletado com sucesso.');
        } catch (\Exception $e) {
            return back()->withErrors(['message' => 'Erro ao deletar recepcionista: ' . $e->getMessage()]);
        }
    }
}
