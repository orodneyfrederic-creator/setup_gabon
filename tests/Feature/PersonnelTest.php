<?php

namespace Tests\Feature;

use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PersonnelTest extends TestCase
{
    use RefreshDatabase;

    private Staff $gerante;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gerante = Staff::create([
            'nom' => 'Mme Ella', 'email' => 'gerante@gmail.com', 'passwd' => 'gerante241', 'role' => Staff::ROLE_GERANTE, 'actif' => true,
        ]);
    }

    public function test_la_gerante_cree_un_compte_vendeur(): void
    {
        $this->actingAs($this->gerante, 'staff')
            ->post(route('gerante.personnel.store'), [
                'nom' => 'Junior',
                'email' => 'Junior@Setup.ga',
                'role' => Staff::ROLE_VENDEUR,
                'mot_de_passe' => 'caisse2026',
                'mot_de_passe_confirmation' => 'caisse2026',
            ])
            ->assertSessionHas('success');

        $vendeur = Staff::where('email', 'junior@setup.ga')->sole();
        $this->assertSame(Staff::ROLE_VENDEUR, $vendeur->role);
        $this->assertTrue(Hash::check('caisse2026', $vendeur->passwd));
    }

    public function test_un_mot_de_passe_trop_faible_est_refuse(): void
    {
        $this->actingAs($this->gerante, 'staff')
            ->post(route('gerante.personnel.store'), [
                'nom' => 'Junior', 'email' => 'junior@setup.ga', 'role' => Staff::ROLE_VENDEUR,
                'mot_de_passe' => 'abc', 'mot_de_passe_confirmation' => 'abc',
            ])
            ->assertSessionHasErrors('mot_de_passe');

        $this->assertSame(1, Staff::count());
    }

    public function test_la_gerante_desactive_un_vendeur_et_reinitialise_son_mot_de_passe(): void
    {
        $vendeur = Staff::create(['nom' => 'Junior', 'email' => 'junior@setup.ga', 'passwd' => 'ancien2026', 'role' => Staff::ROLE_VENDEUR, 'actif' => true]);

        $this->actingAs($this->gerante, 'staff')
            ->put(route('gerante.personnel.update', $vendeur), [
                'role' => Staff::ROLE_VENDEUR, 'actif' => '0',
                'mot_de_passe' => 'nouveau2026', 'mot_de_passe_confirmation' => 'nouveau2026',
            ])
            ->assertSessionHas('success');

        $vendeur->refresh();
        $this->assertFalse($vendeur->estActif());
        $this->assertTrue(Hash::check('nouveau2026', $vendeur->passwd));
    }

    public function test_la_derniere_gerante_active_ne_peut_pas_etre_retiree(): void
    {
        $this->actingAs($this->gerante, 'staff')
            ->put(route('gerante.personnel.update', $this->gerante), ['role' => Staff::ROLE_VENDEUR, 'actif' => '1'])
            ->assertSessionHas('error');

        $this->assertTrue($this->gerante->fresh()->estGerante());
    }

    public function test_une_gerante_ne_peut_pas_se_desactiver_elle_meme(): void
    {
        Staff::create(['nom' => 'Autre', 'email' => 'autre@setup.ga', 'passwd' => 'autre2026x', 'role' => Staff::ROLE_GERANTE, 'actif' => true]);

        $this->actingAs($this->gerante, 'staff')
            ->put(route('gerante.personnel.update', $this->gerante), ['role' => Staff::ROLE_GERANTE, 'actif' => '0'])
            ->assertSessionHas('error');

        $this->assertTrue($this->gerante->fresh()->estActif());
    }

    public function test_un_membre_change_son_mot_de_passe_avec_l_ancien(): void
    {
        $this->actingAs($this->gerante, 'staff')
            ->put(route('gerante.compte.update'), [
                'nom' => 'Mme Ella',
                'mot_de_passe_actuel' => 'mauvais',
                'nouveau_mot_de_passe' => 'secret2026',
                'nouveau_mot_de_passe_confirmation' => 'secret2026',
            ])
            ->assertSessionHasErrors('mot_de_passe_actuel');

        $this->actingAs($this->gerante, 'staff')
            ->put(route('gerante.compte.update'), [
                'nom' => 'Mme Ella',
                'mot_de_passe_actuel' => 'gerante241',
                'nouveau_mot_de_passe' => 'secret2026',
                'nouveau_mot_de_passe_confirmation' => 'secret2026',
            ])
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('secret2026', $this->gerante->fresh()->passwd));
    }
}
