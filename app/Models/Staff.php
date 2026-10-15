<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Staff extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'staff';

    protected $primaryKey = 'id_staff';

    protected $fillable = [
        'email',
        'passwd',
        'role',
    ];

    protected $hidden = [
        'passwd',
    ];

    protected function casts(): array
    {
        return [
            'passwd' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->passwd;
    }

    /**
     * Retourne un nom d'affichage lisible à partir de la partie locale de l'email.
     * Ex: gerante@gmail.com → gerante_SETUP_Gabon
     */
    public function getNomDisplayAttribute(): string
    {
        $prefix = explode('@', $this->email)[0];

        return $prefix.'_SETUP_Gabon';
    }
}
