<?php

namespace Tests\Feature;

use App\Models\Devis;
use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevisRechercheTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_recherche_combinee_au_statut_ne_renvoie_que_ce_statut_et_affiche_le_numero(): void
    {
        $accepte = Devis::create(['numero' => 'DEV-001', 'client_nom' => 'Mboumba', 'statut' => 'accepte', 'montant_total' => 100000]);
        Devis::create(['numero' => 'DEV-002', 'client_nom' => 'Mboumba', 'statut' => 'refuse', 'montant_total' => 50000]);

        $gerante = Staff::create(['email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => 'gerante']);

        $this->actingAs($gerante, 'staff')
            ->get(route('gerante.devis.index', ['search' => 'Mboumba', 'statut' => 'accepte']))
            ->assertOk()
            ->assertViewHas('devis', fn ($devis) => $devis->pluck('id')->all() === [$accepte->id])
            ->assertSee('DEV-001');
    }
}
