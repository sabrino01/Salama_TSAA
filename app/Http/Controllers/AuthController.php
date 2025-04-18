<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nom_utilisateur' => 'required|string',
            'mot_de_passe' => 'required|string',
        ]);

        $user = User::where('nom_utilisateur', $request->nom_utilisateur)->first();

        if (!$user || !Hash::check($request->mot_de_passe, $user->mot_de_passe)) {
            return response()->json([
                'message' => 'Nom d\'utilisateur ou mot de passe incorrect'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Connexion réussie'
        ]);
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Logout successful'], 200);
    }
}
