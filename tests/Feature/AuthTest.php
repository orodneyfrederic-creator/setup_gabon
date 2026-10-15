<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Se connecter');

    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Créer un compte');
    }

    public function test_users_can_register_and_authenticate(): void
    {
        $response = $this->post('/register', [
            'nom' => 'Mboumba',
            'prenom' => 'Jean',
            'email' => 'jean.mboumba@example.ga',
            'tel' => '+24107000000',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'email' => 'jean.mboumba@example.ga',
            'nom' => 'Mboumba',
            'prenom' => 'Jean',
        ]);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.ga',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.ga',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('home'));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.ga',
            'password' => 'password123',
        ]);

        $response = $this->post('/login', [
            'email' => 'user@example.ga',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }
}
