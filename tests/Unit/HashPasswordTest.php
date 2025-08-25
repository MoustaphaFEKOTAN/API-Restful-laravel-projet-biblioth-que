<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class HashPasswordTest extends TestCase
{
    /** @test */
    public function il_hash_correctement_un_mot_de_passe()
    {
        $motDePasse = "monSuperMotDePasse123";

        // Hasher le mot de passe
        $hash = Hash::make($motDePasse);

        // Vérifier que le hash n’est pas égal au mot de passe original
        $this->assertNotEquals($motDePasse, $hash);

        // Vérifier que le hash correspond bien au mot de passe
        $this->assertTrue(Hash::check($motDePasse, $hash));
    }
}
