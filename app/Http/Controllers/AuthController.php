<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 use App\Models\User;
use App\Models\Roles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
  

public function register(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:6|confirmed',
        'role_id' => 'required|exists:roles,id', // ex : "1" ou "2"
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }


    // Créer l'utilisateur
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $request->role_id,
    ]);

    // Générer un token si vous voulez authentifier l'utulisateur automatiquement
    // $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'message' => 'Utilisateur inscrit avec succès',
        'user' => $user,
        // 'token' => $token,
    ], 201);
}

}
