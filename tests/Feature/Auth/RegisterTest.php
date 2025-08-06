<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register_with_valid_data()
    {
        
$response = $this->postJson('/api/register', [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'password',
    'password_confirmation' => 'password',
    'role_id' => 1, // ou un ID existant dans ta base
]);

        $response->assertStatus(200); 
        $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
    }

    /** @test */
    public function it_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422); // erreur de validation
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}
