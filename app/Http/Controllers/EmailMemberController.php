<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\EmailMember;
use Exception;

class EmailMemberController extends Controller
{
   /**
     * Récupère la liste des membres email d'un utilisateur
     */
    public function getMembers($userId)
    {
        try {
            $members = EmailMember::where('user_id', $userId)
                ->pluck('email')
                ->toArray();

            return response()->json($members);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des membres email: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Ajoute un nouveau membre à la liste des destinataires
     */
    public function addMember(Request $request, $userId)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
            ]);

            // Vérifier si l'email existe déjà
            $exists = EmailMember::where('user_id', $userId)
                ->where('email', $validated['email'])
                ->exists();

            if ($exists) {
                return response()->json(['message' => 'Ce membre existe déjà']);
            }

            // Créer le nouveau membre
            EmailMember::create([
                'user_id' => $userId,
                'email' => $validated['email']
            ]);

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'ajout d\'un membre email: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Supprime un membre de la liste des destinataires
     */
    public function removeMember(Request $request, $userId)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|max:255',
            ]);

            $deleted = EmailMember::where('user_id', $userId)
                ->where('email', $validated['email'])
                ->delete();

            if (!$deleted) {
                return response()->json(['error' => 'Membre non trouvé'], 404);
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la suppression d\'un membre email: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }
}
