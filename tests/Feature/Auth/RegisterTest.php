<?php

namespace Tests\Feature\Auth;

use App\Models\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

#[\PHPUnit\Framework\Attributes\Test]
public function a_user_can_register_with_valid_data()
{
    // créer un rôle factice
    $role = Roles::factory()->create([
        'nom' => 'lecteur', 
    ]);

    // Act: appel à l’API
    $response = $this->postJson('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role_id' => $role->id,
    ]);

    // Assert: vérifie que tout est ok
    $response->assertStatus(201); // ou 200
    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
}

  
    public function it_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422); // erreur de validation
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
