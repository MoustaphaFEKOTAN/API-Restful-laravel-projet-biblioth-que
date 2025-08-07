<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Categorie;
use App\Models\Categories;
use App\Models\Nom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\CategorieController
 */
final class CategorieControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $response = $this->get(route('categories.index'));
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CategorieController::class,
            'store',
            \App\Http\Requests\CategorieControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $nom = fake()->word();

        $response = $this->post(route('categories.store'), [
            'nom' => $nom,
        ]);

        $categories = Nom::query()
            ->where('nom', $nom)
            ->get();
        $this->assertCount(1, $categories);
        $categorie = $categories->first();
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $categorie = Categories::factory()->create();

        $response = $this->get(route('categories.show', $categorie));
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\CategorieController::class,
            'update',
            \App\Http\Requests\CategorieControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $categorie = Categories::factory()->create();
        $nom = fake()->word();

        $response = $this->put(route('categories.update', $categorie), [
            'nom' => $nom,
        ]);

        $categorie->refresh();

        $this->assertEquals($nom, $categorie->nom);
    }


    #[Test]
    public function destroy_deletes(): void
    {
        $categorie = Categories::factory()->create();

        $response = $this->delete(route('categories.destroy', $categorie));

        $this->assertModelMissing($categorie);
    }
}
