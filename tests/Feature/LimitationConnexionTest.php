<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LimitationConnexionTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_connexion_est_bloquee_apres_cinq_essais_rates(): void
    {
        User::factory()->create(['email' => 'paul@exemple.ga']);

        for ($essai = 1; $essai <= 5; $essai++) {
            $this->post('/login', ['email' => 'paul@exemple.ga', 'password' => 'mauvais']);
        }

        $this->post('/login', ['email' => 'paul@exemple.ga', 'password' => 'mauvais'])
            ->assertSessionHasErrors(['email' => 'Trop de tentatives de connexion. Patientez une minute avant de réessayer.']);
    }
}
