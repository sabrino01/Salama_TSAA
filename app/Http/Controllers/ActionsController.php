<?php

namespace App\Http\Controllers;

use App\Models\Actions;
use App\Models\Constat;
use App\Models\Responsable;
use App\Models\Sources;
use App\Models\Suivi;
use App\Models\TypeActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActionsController extends Controller
{
    public function indexAI(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // affichage 10 résultats par page

        $actionsAI = Actions::join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->where('actions.num_actions', 'like', "%$search%")
            ->orWhere('actions.date', 'like', "%$search%")
            ->orWhere('actions.description', 'like', "%$search%")
            ->orWhere('actions.statut', 'like', "%$search%")
            ->orWhere('actions.frequence', 'like', "%$search%")
            ->orWhere('users.nom_utilisateur', 'like', "%$search%")
            ->orWhere('constats.libelle', 'like', "%$search%")
            ->select(
                'actions.*',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle'
            )
            ->paginate($perPage);

            // Traiter les données JSON après les avoir récupérées
        foreach ($actionsAI as $action) {
            if ($action->frequence) {
                $frequenceData = json_decode($action->frequence, true);
                $action->frequence_type = $frequenceData['type'] ?? null;
                $action->frequence_debut = $frequenceData['debut'] ?? null;
                $action->frequence_fin = $frequenceData['fin'] ?? null;
            }
        }

        return response()->json($actionsAI);
    }
    public function show($id)
    {
        $action = Actions::find($id);
        if (!$action) {
            return response()->json([
                'message' => 'Action non trouvée'
            ], 404);
        }
        return response()->json($action);
    }
    public function update(Request $request, $id)
    {
        $action = Actions::find($id);
        if (!$action) {
            return response()->json([
                'message' => 'Action non trouvée'
            ], 404);
        }

        $action->update([
            'num_actions' => $request->num_actions,
            'date' => now(),
            'sources_id' => $request->sources_id,
            'type_actions_id' => $request->type_actions_id,
            'responsables_id' => $request->responsables_id,
            'suivis_id' => $request->suivis_id,
            'constats_id' => $request->constats_id,
            'users_id' => Auth::id(),
            'frequence' => $request->frequence,
            'description' => $request->description,
            'observation' => $request->observation,
            'mesure' => $request->mesure,
            'statut' => $request->statut
        ]);

        return response()->json([
            'message' => 'Action mise à jour avec succès',
            'action' => $action
        ]);
    }
    public function destroy($id)
    {
        $action = Actions::find($id);
        if (!$action) {
            return response()->json([
                'message' => 'Action non trouvée'
            ], 404);
        }

        $action->delete();

        return response()->json([
            'message' => 'Action supprimée avec succès'
        ]);
    }

    public function createAI()
    {
        // Récupérer le dernier numéro d'action enregistré
        $lastAction = Actions::latest('id')->first();

        if ($lastAction && preg_match('/AI-(\d+)/', $lastAction->num_actions, $matches)) {
            // Extraire la partie numérique et incrémenter
            $lastNumber = (int) $matches[1];
            $numActions = 'AI-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Si aucun enregistrement n'existe, commencer par AI-0001
            $numActions = 'AI-0001';
        }

        // Récupérer les données nécessaires pour les champs select
        $sources = Sources::where('sources_pour', "auditinterne")->get();
        $typeActions = TypeActions::where('typeactions_pour','auditinterne')->get();
        $responsables = Responsable::all();
        $suivis = Suivi::all();
        $constats = Constat::all();

        return response()->json([
            'num_actions'  => $numActions,
            'sources' => $sources,
            'typeActions' => $typeActions,
            'responsables' => $responsables,
            'suivis' => $suivis,
            'constats' => $constats
        ]);
    }

    public function store(Request $request)
    {
        // Log::info($request->all());
        // Log::info('ID utilisateur authentifié : ' . Auth::id());

        $action = Actions::create([
            'num_actions'  => $request->num_actions,
            'date' => now(),
            'sources_id' => $request->sources_id,
            'type_actions_id' => $request->type_actions_id,
            'responsables_id' => $request->responsables_id,
            'suivis_id' => $request->suivis_id,
            'constats_id' => $request->constats_id,
            'users_id' => $request->users_id,
            'frequence' => $request->frequence,
            'description' => $request->description,
            'observation' => $request->observation,
            'mesure' => $request->mesure,
            'statut' => 'En cours'
        ]);

        return response()->json([
            'message' => 'Actions ajouté avec succès',
            'action' => $action
        ]);
    }
}
