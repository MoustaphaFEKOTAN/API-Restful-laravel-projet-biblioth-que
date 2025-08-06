<?php

namespace Tests\Feature;

use App\Models\Livre;
use App\Models\Livres;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivreTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Création utilisateur pour authentification
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_lists_livres()
    {
        Livres::factory()->count(3)->create();

        $response = $this->getJson('/api/livres');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data'); // selon ta structure JSON
    }

    /** @test */
    public function authenticated_user_can_create_livre()
    {
        $livreData = [
            'titre' => 'Nouveau livre',
            'auteur' => 'Auteur Exemple',
            'description' => 'Une description ici',
            // ajoute les champs nécessaires
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/livres', $livreData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['titre' => 'Nouveau livre']);

        $this->assertDatabaseHas('livres', ['titre' => 'Nouveau livre']);
    }

    /** @test */
    public function it_shows_a_single_livre()
    {
        $livre = Livres::factory()->create();

        $response = $this->getJson("/api/livres/{$livre->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $livre->id]);
    }

    /** @test */
    public function authenticated_user_can_update_livre()
    {
        $livre = Livres::factory()->create();

        $updateData = [
            'titre' => 'Titre modifié',
            // autres champs modifiés
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/livres/{$livre->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['titre' => 'Titre modifié']);

        $this->assertDatabaseHas('livres', ['id' => $livre->id, 'titre' => 'Titre modifié']);
    }


    public function authenticated_user_can_delete_livre()
    {
        $livre = Livres::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson("/api/livres/{$livre->id}");

        $response->assertStatus(204); // No Content

        $this->assertDatabaseMissing('livres', ['id' => $livre->id]);
    }
}
