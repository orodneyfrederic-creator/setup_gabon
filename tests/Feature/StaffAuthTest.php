<?php

use App\Models\Staff;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('staff user can authenticate and access gerante dashboard', function () {
    $staff = Staff::create([
        'email' => 'gerante@gmail.com',
        'passwd' => Hash::make('gerante241'),
        'role' => 'gerante',
    ]);

    $response = $this->post('/login', [
        'email' => 'gerante@gmail.com',
        'password' => 'gerante241',
    ]);

    $response->assertRedirect(route('gerante.dashboard'));
    $this->assertAuthenticatedAs($staff, 'staff');

    $dashboardResponse = $this->actingAs($staff, 'staff')->get(route('gerante.dashboard'));
    $dashboardResponse->assertStatus(200);
});
