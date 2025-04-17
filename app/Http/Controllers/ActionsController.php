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

class ActionsController extends Controller
{
    public function createAI()
    {
        // Générer le numéro d'action
        $lastAction = Actions::latest('id')->first();
        $numActions = 'AI-' . str_pad(($lastAction ? $lastAction->id + 1 : 1), 4, '0', STR_PAD_LEFT);


        // Récupérer les données nécessaires pour les champs select
        $sources = Sources::where('sources_pour', "auditinterne")->get();
        $typeActions = TypeActions::where('typeactions_pour','auditinterne')->get();
        $responsables = Responsable::all();
        $suivis = Suivi::all();
        $constats = Constat::all();

        return response()->json([
            'numActions' => $numActions,
            'sources' => $sources,
            'typeActions' => $typeActions,
            'responsables' => $responsables,
            'suivis' => $suivis,
            'constats' => $constats
        ]);
    }

    public function store(Request $request)
    {
        $action = Actions::create([
            'num_actions'  => $request->numActions,
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
            'statut' => 'En cours'
        ]);

        return response()->json([
            'message' => 'Actions ajouté avec succès',
            'action' => $action
        ]);
    }
}
