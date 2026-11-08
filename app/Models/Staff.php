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

    public const ROLE_GERANTE = 'gerante';

    public const ROLE_VENDEUR = 'vendeur';

    /**
     * Rôles du personnel et ce qu'ils permettent.
     *
     * @var array<string, string>
     */
    public const ROLES = [
        self::ROLE_GERANTE => 'Gérante : accès complet',
        self::ROLE_VENDEUR => 'Vendeur : caisse, commandes en ligne, stock et SAV',
    ];

    protected $fillable = [
        'nom',
        'email',
        'passwd',
        'role',
        'actif',
    ];

    protected $hidden = [
        'passwd',
    ];

    protected function casts(): array
    {
        return [
            'passwd' => 'hashed',
            'actif' => 'boolean',
        ];
    }

    public function estGerante(): bool
    {
        return $this->role === self::ROLE_GERANTE;
    }

    /**
     * Un compte créé avant l'ajout de la colonne « actif » (valeur nulle) reste actif.
     */
    public function estActif(): bool
    {
        return $this->actif !== false;
    }

    public function libelleRole(): string
    {
        return match ($this->role) {
            self::ROLE_GERANTE => 'Gérante',
            self::ROLE_VENDEUR => 'Vendeur',
            default => ucfirst((string) $this->role),
        };
    }

    /**
     * Page d'arrivée après connexion : le tableau de bord pour la gérante, la caisse pour un vendeur.
     */
    public function routeAccueil(): string
    {
        return $this->estGerante() ? 'gerante.dashboard' : 'gerante.pos.index';
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
