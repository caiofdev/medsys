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
        
        $userType = session()->remember('user_type', function() use ($user) {
            $user->load(['admin', 'doctor', 'receptionist']);
            return $user->getUserType();
        });

        if (!in_array($userType, $types)) {
            return redirect()->route($this->getRedirectRoute($userType));
        }

        return $next($request);
    }

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
