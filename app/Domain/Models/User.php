<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'password',
        'birth_date',
        'phone',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function receptionist()
    {
        return $this->hasOne(Receptionist::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function getUserType(): ?string
    {
        // OTIMIZAÇÃO CRÍTICA: Verifica relações carregadas primeiro
        if ($this->relationLoaded('admin') && $this->admin) {
            return 'admin';
        }
        
        if ($this->relationLoaded('doctor') && $this->doctor) {
            return 'doctor';
        }
        
        if ($this->relationLoaded('receptionist') && $this->receptionist) {
            return 'receptionist';
        }
        
        // OTIMIZAÇÃO: Carregar todas as relações de uma vez ao invés de 3 queries separadas
        $this->load(['admin', 'doctor', 'receptionist']);
        
        if ($this->admin) {
            return 'admin';
        }
        
        if ($this->doctor) {
            return 'doctor';
        }
        
        if ($this->receptionist) {
            return 'receptionist';
        }
        
        return null;
    }

    public function isAdmin(): bool
    {
        return $this->admin()->exists();
    }

    public function isDoctor(): bool
    {
        return $this->doctor()->exists();
    }

    public function isReceptionist(): bool
    {
        return $this->receptionist()->exists();
    }
}