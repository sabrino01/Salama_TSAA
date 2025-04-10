<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 4); // Permettre de définir le nombre d'éléments par page

        $users = User::where('nom', 'like', "%$search%")
            ->orWhere('nom_utilisateur', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('departement', 'like', "%$search%")
            ->paginate($perPage);

        return response()->json($users);
    }

    public function create()
    {
        // Code to show the form for creating a new user
    }

    public function store(Request $request)
    {
        $validateData = $request->validate([
            'nom' => 'required|string|max:255|unique:users',
            'nom_utilisateur' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'departement' => 'required|string|max:255',
            'mot_de_passe' => 'required|string|min:8',
        ]);

         // Vérifier manuellement les mots de passe
    if ($request->mot_de_passe !== $request->confirmer_mot_de_passe) {
        return response()->json([
            'message' => 'Les mots de passe ne correspondent pas',
            'errors' => [
                'confirmer_mot_de_passe' => ['Les mots de passe ne correspondent pas']
            ]
        ], 422);
    }

        User::create([
            'nom' => $validateData['nom'],
            'nom_utilisateur' => $validateData['nom_utilisateur'],
            'email' => $validateData['email'],
            'departement' => $validateData['departement'],
            'mot_de_passe' => bcrypt($validateData['mot_de_passe']),
        ]);
        return response()->json([
            'message' => 'Utilisateur ajouté avec succès',
        ], 201);
    }

    public function show($id)
    {
        // Code to display a specific user
    }

    public function edit($id)
    {
        // Code to show the form for editing a specific user
    }

    public function update(Request $request, $id)
    {
        // Code to update a specific user in the database
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            'message' => 'Utilisateur supprimé avec succès',
        ], 200);
    }
    public function search(Request $request)
    {
        // Code to search for users based on the request parameters
    }
}
