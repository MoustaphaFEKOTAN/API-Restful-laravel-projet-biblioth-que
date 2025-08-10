<?php

namespace Tests\Feature\Endpoint;

use App\Models\Categories;
use App\Models\Livres;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class LivreTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

$role = Roles::factory()->create([
    'nom' =>'auteur',
]);

        // Création utilisateur pour authentification
        $this->user = User::factory()->create([
            'role_id' => $role->id,
        ]);
    }

  #[\PHPUnit\Framework\Attributes\Test]
    public function it_lists_livres()
    {
        Livres::factory()->count(3)->create();

        $response = $this->getJson('/api/livres');
        

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

  #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_create_livre()
    {
        $livreData = [
            'titre' => 'Nouveau livre',
            'auteur' => 'Auteur Exemple',
            'description' => 'Une description ici',
            'date_sortie' =>'2025-08-01',
            'slug' => (string) Str::uuid(),
            'categorie_id' =>  Categories::factory()->create()->id,
             'user_id' => $this->user->id,
        ];

// dd($this->user);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/livres/store', $livreData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['titre' => 'Nouveau livre']);

        $this->assertDatabaseHas('livres', ['titre' => 'Nouveau livre']);
    }

#[\PHPUnit\Framework\Attributes\Test]
    public function it_shows_a_single_livre()
    {
        $livre = Livres::factory()->create();

        $response = $this->getJson('/api/livres/' . $livre->slug);

        $response->assertStatus(200)
                 ->assertJsonFragment(['slug' => $livre->slug]);
    }

    

   #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_update_livre()
    {
        $livre = Livres::factory()->create();

        $updateData = [
           'titre' => 'titre mis a jour',
            'auteur' => 'Auteur ',
            'description' => 'Une description ici',
            'date_sortie' =>'2025-08-01',
            'slug' => (string) Str::uuid(),
            'categorie_id' =>  Categories::factory()->create()->id,
             'user_id' => $this->user->id,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson('/api/livres/'. $livre->slug, $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['titre' => 'titre mis a jour']);

        $this->assertDatabaseHas('livres', ['slug' => $livre->slug, 'titre' => 'titre mis a jour']);
    }

 #[\PHPUnit\Framework\Attributes\Test]
    public function authenticated_user_can_delete_livre()
    {
       $livre = Livres::factory()->create([
        'user_id' => $this->user,
    ]);

        $response = $this->actingAs($this->user, 'sanctum') //Authentifie l'utulisateur
                         ->deleteJson('/api/livres/'. $livre->slug);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('livres', ['slug' => $livre->slug]);
    }
}
