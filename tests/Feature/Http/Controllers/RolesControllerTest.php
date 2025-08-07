<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Nom;

use App\Models\Roles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RolesController
 */
final class RolesControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_behaves_as_expected(): void
    {
        $response = $this->get(route('roles.index'));
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RolesController::class,
            'store',
            \App\Http\Requests\RolesControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves(): void
    {
        $nom = fake()->word();

        $response = $this->post(route('roles.store'), [
            'nom' => $nom,
        ]);

        $roles = Nom::query()
            ->where('nom', $nom)
            ->get();
        $this->assertCount(1, $roles);
        $role = $roles->first();
    }


    #[Test]
    public function show_behaves_as_expected(): void
    {
        $role = Roles::factory()->create();
       

        $response = $this->get(route('roles.show', $role));
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RolesController::class,
            'update',
            \App\Http\Requests\RolesControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_behaves_as_expected(): void
    {
        $role = Roles::factory()->create();
        $nom = fake()->word();

        $response = $this->put(route('roles.update', $role), [
            'nom' => $nom,
        ]);

        $role->refresh();

        $this->assertEquals($nom, $role->nom);
    }


    #[Test]
    public function destroy_deletes(): void
    {
        $role = Roles::factory()->create();
       

        $response = $this->delete(route('roles.destroy', $role));

        $this->assertModelMissing($role);
    }
}
