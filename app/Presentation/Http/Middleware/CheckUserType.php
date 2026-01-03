<?php

namespace App\Presentation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$types): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $userType = $user->getUserType();

        // Verificar se o tipo do usuário está na lista de tipos permitidos
        if (!in_array($userType, $types)) {
            // Redirecionar para a dashboard apropriada baseada no tipo do usuário
            return redirect()->route($this->getRedirectRoute($userType));
        }

        return $next($request);
    }

    /**
     * Obter a rota de redirecionamento baseada no tipo de usuário
     */
    private function getRedirectRoute(string $userType): string
    {
        return match($userType) {
            'admin' => 'admin.dashboard',
            'doctor' => 'doctor.dashboard',
            'patient' => 'patient.dashboard',
            'receptionist' => 'receptionist.dashboard',
            default => 'login',
        };
    }
}
