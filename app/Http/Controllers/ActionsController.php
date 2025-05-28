<?php

namespace App\Http\Controllers;

use App\Models\Actions;
use App\Models\Actions_responsable;
use App\Models\Actions_suivi;
use App\Models\Constat;
use App\Models\Responsable;
use App\Models\Sources;
use App\Models\Suivi;
use App\Models\TypeActions;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as FacadesLog;

class ActionsController extends Controller
{
    public function indexAI(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // affichage 10 résultats par page

        $actionsAI = Actions::join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.num_actions', 'like', 'AI-%') // Filtrer uniquement les num_actions commençant par AI-
            ->where(function ($query) use ($search) {
                $query->where('actions.num_actions', 'like', "%$search%")
                      ->orWhere('actions.date', 'like', "%$search%")
                      ->orWhere('actions.description', 'like', "%$search%")
                        ->orWhere('actions.observation', 'like', "%$search%")
                        ->orWhere('actions.mesure', 'like', "%$search%")
                        ->orWhere('sources.libelle', 'like', "%$search%")
                        ->orWhere('type_actions.libelle', 'like', "%$search%")
                      ->orWhere('actions.statut', 'like', "%$search%")
                      ->orWhere('actions.frequence', 'like', "%$search%")
                      ->orWhere('actions.observation_par_suivi', 'like', "%$search%")
                      ->orWhere('users.nom_utilisateur', 'like', "%$search%")
                      ->orWhere('constats.libelle', 'like', "%$search%")
                       // Recherche dans les responsables
                    ->orWhereExists(function ($subquery) use ($search) {
                        $subquery->select(DB::raw(1))
                                ->from('responsables')
                                ->whereRaw("CHARINDEX(CAST(responsables.id AS VARCHAR), actions.responsables_id) > 0")
                                ->where('responsables.libelle', 'like', "%$search%");
                    })
                    // Recherche dans les suivis
                    ->orWhereExists(function ($subquery) use ($search) {
                        $subquery->select(DB::raw(1))
                                ->from('suivis')
                                ->whereRaw("CHARINDEX(CAST(suivis.id AS VARCHAR), actions.suivis_id) > 0")
                                ->where('suivis.nom', 'like', "%$search%");
                    });
            })
            ->select(
                'actions.*',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle'
            )
            ->orderBy('actions.id', 'desc') // Trier par ordre décroissant
            ->paginate($perPage);

          // Enrichir les résultats avec les noms des responsables et suivis
            $actionsAI->getCollection()->transform(function ($action) {
                $responsablesIds = array_filter(explode(',', $action->responsables_id));
                $suivisIds = array_filter(explode(',', $action->suivis_id));

                $responsables = Responsable::whereIn('id', $responsablesIds)->pluck('libelle')->toArray();
                $suivis = Suivi::whereIn('id', $suivisIds)->pluck('nom')->toArray();

                $action->responsables_libelle = implode(', ', $responsables);
                $action->suivis_noms = implode(', ', $suivis);

                return $action;
            });

    //         $actionsForLog = $actionsAI->toArray();

    //   FacadesLog::info('Actions AI:',$actionsForLog);

        return response()->json($actionsAI);
    }

    public function indexPTA(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // affichage 10 résultats par page

        $actionsPTA = Actions::join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.num_actions', 'like', 'PTA-%') // Filtrer uniquement les num_actions commençant par AI-
            ->where(function ($query) use ($search) {
                $query->where('actions.num_actions', 'like', "%$search%")
                      ->orWhere('actions.date', 'like', "%$search%")
                      ->orWhere('actions.description', 'like', "%$search%")
                        ->orWhere('actions.observation', 'like', "%$search%")
                        ->orWhere('actions.mesure', 'like', "%$search%")
                        ->orWhere('sources.libelle', 'like', "%$search%")
                        ->orWhere('type_actions.libelle', 'like', "%$search%")
                      ->orWhere('actions.statut', 'like', "%$search%")
                      ->orWhere('actions.frequence', 'like', "%$search%")
                      ->orWhere('actions.observation_par_suivi', 'like', "%$search%")
                      ->orWhere('users.nom_utilisateur', 'like', "%$search%")
                      ->orWhere('constats.libelle', 'like', "%$search%")
                       // Recherche dans les responsables
                    ->orWhereExists(function ($subquery) use ($search) {
                        $subquery->select(DB::raw(1))
                                ->from('responsables')
                                ->whereRaw("CHARINDEX(CAST(responsables.id AS VARCHAR), actions.responsables_id) > 0")
                                ->where('responsables.libelle', 'like', "%$search%");
                    })
                    // Recherche dans les suivis
                    ->orWhereExists(function ($subquery) use ($search) {
                        $subquery->select(DB::raw(1))
                                ->from('suivis')
                                ->whereRaw("CHARINDEX(CAST(suivis.id AS VARCHAR), actions.suivis_id) > 0")
                                ->where('suivis.nom', 'like', "%$search%");
                    });
            })
            ->select(
                'actions.*',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle'
            )
            ->orderBy('actions.id', 'desc') // Trier par ordre décroissant
            ->paginate($perPage);

          // Enrichir les résultats avec les noms des responsables et suivis
            $actionsPTA->getCollection()->transform(function ($action) {
                $responsablesIds = array_filter(explode(',', $action->responsables_id));
                $suivisIds = array_filter(explode(',', $action->suivis_id));

                $responsables = Responsable::whereIn('id', $responsablesIds)->pluck('libelle')->toArray();
                $suivis = Suivi::whereIn('id', $suivisIds)->pluck('nom')->toArray();

                $action->responsables_libelle = implode(', ', $responsables);
                $action->suivis_noms = implode(', ', $suivis);

                return $action;
            });

    //         $actionsForLog = $actionsPTA->toArray();

    //   FacadesLog::info('Actions PTA:',$actionsForLog);

        return response()->json($actionsPTA);
    }

   public function show($id)
{
    $action = Actions::with('user', 'constat', 'sources', 'type_actions')->find($id);

    if (!$action) {
        return response()->json(['message' => 'Action non trouvée'], 404);
    }

    // Traitement des responsables
    $responsablesIds = explode(',', $action->responsables_id);
    $responsables = Responsable::whereIn('id', $responsablesIds)->pluck('libelle')->implode(', ');

    // Traitement des suivis
    $suivisIds = explode(',', $action->suivis_id);
    $suivis = Suivi::whereIn('id', $suivisIds)->pluck('nom')->implode(', ');

    // On peut aussi renommer les libellés si nécessaire
    $action->responsable_libelle = $responsables;
    $action->suivi_nom = $suivis;
    $action->constat_libelle = $action->constat->libelle ?? null;
    $action->source_libelle = $action->sources->libelle ?? null;
    $action->type_action_libelle = $action->type_actions->libelle ?? null;
    $action->nom_utilisateur = $action->user->nom_utilisateur ?? null;

    // Récupérer les responsables qui ont mis à jour leur statut et observations
    $responsablesUpdates = DB::table('actions_responsables')
        ->join('responsables', 'actions_responsables.responsables_id', '=', 'responsables.id')
        ->where('actions_responsables.actions_id', $id)
        ->whereNotNull('actions_responsables.statut_resp')
        ->whereNotNull('actions_responsables.observation_resp')
        ->where('actions_responsables.statut_resp', '!=', '')
        ->where('actions_responsables.observation_resp', '!=', '')
        ->select(
            'actions_responsables.responsables_id',
            'actions_responsables.statut_resp',
            'actions_responsables.observation_resp',
            'responsables.libelle as responsable_nom',
            'actions_responsables.updated_at as date_update'
        )
        ->get();

    // Ajouter les données des responsables updates directement à l'action
    $action->responsables_updates = $responsablesUpdates;
    $action->has_responsables_updates = $responsablesUpdates->count() > 0;

    // Récupérer les suivis qui ont mis à jour leur statut et observations
    $suivisUpdates = DB::table('actions_suivis')
        ->join('suivis', 'actions_suivis.suivis_id', '=', 'suivis.id')
        ->where('actions_suivis.actions_id', $id)
        ->whereNotNull('actions_suivis.statut_suivi')
        ->whereNotNull('actions_suivis.observation_suivi')
        ->where('actions_suivis.statut_suivi', '!=', '')
        ->where('actions_suivis.observation_suivi', '!=', '')
        ->select(
            'actions_suivis.suivis_id',
            'actions_suivis.statut_suivi',
            'actions_suivis.observation_suivi',
            'suivis.nom as suivi_nom',
            'actions_suivis.updated_at as date_update'
        )
        ->get();

    // Ajouter les données des suivis updates directement à l'action
    $action->suivis_updates = $suivisUpdates;
    $action->has_suivis_updates = $suivisUpdates->count() > 0;

    // Variable globale pour savoir s'il y a des mises à jour (responsables OU suivis)
    $action->has_updates = $action->has_responsables_updates || $action->has_suivis_updates;

    // Traiter les observations par suivi pour l'affichage (seulement si il y a des mises à jour)
    if ($action->has_updates && $action->observation_par_suivi) {
        $action->observations_suivi = json_decode($action->observation_par_suivi, true);
        $action->has_observations_suivi = count($action->observations_suivi) > 0;
    } else {
        $action->observations_suivi = [];
        $action->has_observations_suivi = false;
    }

    return response()->json($action);
}

    public function update(Request $request, $id)
{
    $action = Actions::with('user', 'constat', 'sources', 'type_actions')->find($id);

    if (!$action) {
        return response()->json([
            'message' => 'Action non trouvée'
        ], 404);
    }

    // Vérifie si les champs sont envoyés comme tableau et les transforme en chaîne
    $responsables = is_array($request->responsables_id)
        ? implode(',', $request->responsables_id)
        : $request->responsables_id;

    $suivis = is_array($request->suivis_id)
        ? implode(',', $request->suivis_id)
        : $request->suivis_id;

    // Gérer observation_par_suivi (nouveau champ JSON)
    $observationParSuivi = $action->observation_par_suivi;

    // Gestion de la suppression d'une observation
    if ($request->has('supprimer_observation_index') && $request->supprimer_observation_index !== null) {
        $indexToDelete = (int) $request->supprimer_observation_index;

        // Décoder le JSON existant
        $observations = $observationParSuivi ? json_decode($observationParSuivi, true) : [];

        // Vérifier si l'index existe
        if (isset($observations[$indexToDelete])) {
            // Supprimer l'observation à l'index spécifié
            array_splice($observations, $indexToDelete, 1);

            // Réencoder en JSON
            $observationParSuivi = empty($observations) ? null : json_encode($observations);
        } else {
            return response()->json([
                'message' => 'Index d\'observation invalide'
            ], 400);
        }
    }
    // Si une nouvelle observation de suivi est envoyée
    elseif ($request->has('nouvelle_observation_suivi') && $request->nouvelle_observation_suivi) {
        // Vérifier s'il y a des mises à jour de responsables OU de suivis
        $hasResponsableUpdates = DB::table('actions_responsables')
            ->where('actions_id', $id)
            ->whereNotNull('statut_resp')
            ->whereNotNull('observation_resp')
            ->where('statut_resp', '!=', '')
            ->where('observation_resp', '!=', '')
            ->exists();

        $hasSuivisUpdates = DB::table('actions_suivis')
            ->where('actions_id', $id)
            ->whereNotNull('statut_suivi')
            ->whereNotNull('observation_suivi')
            ->where('statut_suivi', '!=', '')
            ->where('observation_suivi', '!=', '')
            ->exists();

        if ($hasResponsableUpdates || $hasSuivisUpdates) {
            // Décoder le JSON existant ou créer un nouveau tableau
            $observations = $observationParSuivi ? json_decode($observationParSuivi, true) : [];

            // Validation de la date envoyée depuis le frontend
            $dateObservation = $request->date_observation_suivi;

            // Si aucune date n'est fournie, utiliser la date actuelle
            if (!$dateObservation) {
                $dateObservation = now()->format('Y-m-d H:i:s');
            } else {
                // Valider et formater la date reçue
                try {
                    $dateObservation = Carbon::createFromFormat('Y-m-d', $dateObservation)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    // Si le format n'est pas valide, essayer avec d'autres formats
                    try {
                        $dateObservation = Carbon::parse($dateObservation)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        return response()->json([
                            'message' => 'Format de date invalide. Utilisez le format YYYY-MM-DD.'
                        ], 400);
                    }
                }
            }

            // Ajouter la nouvelle observation avec la date choisie
            $nouvelleObservation = [
                'date' => $dateObservation,
                'observation' => $request->nouvelle_observation_suivi,
            ];

            $observations[] = $nouvelleObservation;
            $observationParSuivi = json_encode($observations);
        } else {
            return response()->json([
                'message' => 'Impossible d\'ajouter une observation de suivi. Aucune mise à jour de responsable ou de suivi trouvée.'
            ], 400);
        }
    }

    $action->update([
        'sources_id' => $request->sources_id,
        'type_actions_id' => $request->type_actions_id,
        'responsables_id' => $responsables,
        'suivis_id' => $suivis,
        'constats_id' => $request->constats_id,
        'frequence' => $request->frequence,
        'description' => $request->description,
        'observation' => $request->observation,
        'mesure' => $request->mesure,
        'statut' => $request->statut,
        'observation_par_suivi' => $observationParSuivi
    ]);

    // Recharger l'action avec les relations pour avoir les données fraîches
    $action = Actions::with('user', 'constat', 'sources', 'type_actions')->find($id);

    // Traitement des responsables (comme dans show)
    $responsablesIds = explode(',', $action->responsables_id);
    $responsables = Responsable::whereIn('id', $responsablesIds)->pluck('libelle')->implode(', ');

    // Traitement des suivis (comme dans show)
    $suivisIds = explode(',', $action->suivis_id);
    $suivis = Suivi::whereIn('id', $suivisIds)->pluck('nom')->implode(', ');

    // Ajouter les libellés
    $action->responsable_libelle = $responsables;
    $action->suivi_nom = $suivis;
    $action->constat_libelle = $action->constat->libelle ?? null;
    $action->source_libelle = $action->sources->libelle ?? null;
    $action->type_action_libelle = $action->type_actions->libelle ?? null;
    $action->nom_utilisateur = $action->user->nom_utilisateur ?? null;

    // Récupérer les responsables qui ont mis à jour leur statut et observations
    $responsablesUpdates = DB::table('actions_responsables')
        ->join('responsables', 'actions_responsables.responsables_id', '=', 'responsables.id')
        ->where('actions_responsables.actions_id', $id)
        ->whereNotNull('actions_responsables.statut_resp')
        ->whereNotNull('actions_responsables.observation_resp')
        ->where('actions_responsables.statut_resp', '!=', '')
        ->where('actions_responsables.observation_resp', '!=', '')
        ->select(
            'actions_responsables.responsables_id',
            'actions_responsables.statut_resp',
            'actions_responsables.observation_resp',
            'responsables.libelle as responsable_nom',
            'actions_responsables.updated_at as date_update'
        )
        ->get();

    // Récupérer les suivis qui ont mis à jour leur statut et observations
    $suivisUpdates = DB::table('actions_suivis')
        ->join('suivis', 'actions_suivis.suivis_id', '=', 'suivis.id')
        ->where('actions_suivis.actions_id', $id)
        ->whereNotNull('actions_suivis.statut_suivi')
        ->whereNotNull('actions_suivis.observation_suivi')
        ->where('actions_suivis.statut_suivi', '!=', '')
        ->where('actions_suivis.observation_suivi', '!=', '')
        ->select(
            'actions_suivis.suivis_id',
            'actions_suivis.statut_suivi',
            'actions_suivis.observation_suivi',
            'suivis.nom as suivi_nom',
            'actions_suivis.updated_at as date_update'
        )
        ->get();

    // Ajouter les données des responsables et suivis updates à l'action
    $action->responsables_updates = $responsablesUpdates;
    $action->suivis_updates = $suivisUpdates;
    $action->has_responsables_updates = $responsablesUpdates->count() > 0;
    $action->has_suivis_updates = $suivisUpdates->count() > 0;
    $action->has_updates = $action->has_responsables_updates || $action->has_suivis_updates;

    // Traiter les observations par suivi pour l'affichage
    if ($action->observation_par_suivi) {
        $action->observations_suivi = json_decode($action->observation_par_suivi, true);
        $action->has_observations_suivi = count($action->observations_suivi) > 0;
    } else {
        $action->observations_suivi = [];
        $action->has_observations_suivi = false;
    }

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
        // Récupérer le dernier numéro d'action au format AI-XXXX
        $lastAction = Actions::where('num_actions', 'like', 'AI-%')
        ->orderByRaw('TRY_CAST(SUBSTRING(num_actions, 5, LEN(num_actions) - 4) AS INT) DESC')
                    ->first();

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

    public function createPTA()
    {
       // Récupérer le dernier numéro d'action au format PTA-XXXX
        $lastAction = Actions::where('num_actions', 'like', 'PTA-%')
        ->orderByRaw('TRY_CAST(SUBSTRING(num_actions, 5, LEN(num_actions) - 4) AS INT) DESC')
        ->first();

        if ($lastAction && preg_match('/PTA-(\d+)/', $lastAction->num_actions, $matches)) {
            // Extraire la partie numérique et incrémenter
            $lastNumber = (int) $matches[1];
            $numActions = 'PTA-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            // Si aucun enregistrement n'existe, commencer par PTA-0001
            $numActions = 'PTA-0001';
        }

        // Récupérer les données nécessaires pour les champs select
        $sources = Sources::where('sources_pour', "pta")->get();
        $typeActions = TypeActions::where('typeactions_pour','pta')->get();
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
           'responsables_id' => is_array($request->responsables_id)
                ? implode(',', $request->responsables_id)
                : null,
            'suivis_id' => is_array($request->suivis_id)
                ? implode(',', $request->suivis_id)
                : null,
            'constats_id' => $request->constats_id,
            'users_id' => $request->users_id,
            'frequence' => $request->frequence,
            'description' => $request->description,
            'observation' => $request->observation,
            'mesure' => $request->mesure,
            'statut' => 'En cours'
        ]);

         // Créer les enregistrements dans la table actions_responsables
        if ($request->responsables_id && is_array($request->responsables_id)) {
            foreach ($request->responsables_id as $responsable_id) {
                Actions_responsable::create([
                    'actions_id' => $action->id,
                    'responsables_id' => $responsable_id,
                    'statut_resp' => 'En cours',
                    'observation_resp' => null
                ]);
            }
        }

        if ($request->suivis_id && is_array($request->suivis_id)) {
            foreach ($request ->suivis_id as $suivi_id) {
                Actions_suivi::create([
                    'actions_id' => $action->id,
                    'suivis_id' => $suivi_id,
                    'statut_suivi' => 'En cours',
                    'observation_suivi' => null
                ]);
            }
        }

        return response()->json([
            'message' => 'Actions ajouté avec succès',
            'action' => $action
        ]);
    }

    public function sourcesAI()
    {
        $sourcesAI = Sources::where('sources_pour', "auditinterne")->get();
        return response()->json($sourcesAI);
    }
    public function typeActionsAI()
    {
        $typeActionsAI = TypeActions::where('typeactions_pour','auditinterne')->get();
        return response()->json($typeActionsAI);
    }

    public function sourcesPTA()
    {
        $sourcesAI = Sources::where('sources_pour', "pta")->get();
        return response()->json($sourcesAI);
    }
    public function typeActionsPTA()
    {
        $typeActionsAI = TypeActions::where('typeactions_pour','pta')->get();
        return response()->json($typeActionsAI);
    }

    public function responsables()
    {
        $responsables = Responsable::all();
        return response()->json($responsables);
    }
    public function suivis()
    {
        $suivis = Suivi::all();
        return response()->json($suivis);
    }
    public function constats()
    {
        $constats = Constat::all();
        return response()->json($constats);
    }

    public function users()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function checkExistingNumActions(Request $request)
    {
        $numActions = $request->input('num_actions');

        if (!$numActions || !is_array($numActions)) {
            return response()->json(['message' => 'Données invalides.'], 400);
        }

        // Récupérer les num_actions existants dans la base de données
        $existingNumActions = Actions::whereIn('num_actions', $numActions)->pluck('num_actions');

        return response()->json($existingNumActions);
    }

    public function import(Request $request)
    {
        $data = $request->input('data');

        if (!$data || !is_array($data)) {
            return response()->json(['message' => 'Données invalides.'], 400);
        }

        $newRecords = [];
        foreach ($data as $row) {
             // Convertir les libellés en IDs
             $sourceId = Sources::where('libelle', $row['source_libelle'])->value('id');
             $typeActionId = TypeActions::where('libelle', $row['type_action_libelle'])->value('id');
            $responsableNoms = array_map('trim', explode(',', $row['responsables_libelle']));
            $responsableIds = Responsable::whereIn('libelle', $responsableNoms)->pluck('id')->toArray();
            $suiviNoms = array_map('trim', explode(',', $row['suivis_noms']));
            $suiviIds = Suivi::whereIn('nom', $suiviNoms)->pluck('id')->toArray();
             $constatId = Constat::where('libelle', $row['constat_libelle'])->value('id');
             $userId = User::where('nom_utilisateur', $row['nom_utilisateur'])->value('id');

             // Vérifier si l'enregistrement existe déjà
             $exists = Actions::where('num_actions', $row['num_actions'])->exists();

            //  $frequence = $row['frequence'];

            // Si on veux que l'importation du frequence est un json
            // if (!empty($frequence)) {
            //     // Vérifier si c'est déjà un format JSON
            //     if (!in_array(substr($frequence, 0, 1), ['{', '['])) {
            //         // Si c'est une chaîne simple, convertir en JSON
            //         $frequence = json_encode(['type' => $frequence]);
            //     }
            // }

             if (!$exists) {
                 $newRecords[] = [
                     'num_actions' => $row['num_actions'],
                     'date' => $row['date'], // Convertir la date au format Y-m-d
                     'sources_id' => $sourceId,
                     'type_actions_id' => $typeActionId,
                     'responsables_id' => implode(',', $responsableIds),
                    'suivis_id' => implode(',', $suiviIds),
                     'description' => $row['description'],
                     'constats_id' => $constatId,
                     'frequence' => $row['frequence'],
                     'mesure' => $row['mesure'],
                     'statut' => $row['statut'],
                     'users_id' => $userId,
                    'observation' => $row['observation'],
                     'created_at' => now(),
                     'updated_at' => now(),
                 ];
             }
         }

        // Insérer les nouveaux enregistrements
        if (!empty($newRecords)) {
            // FacadesLog::info('Enregistrements à insérer :', $newRecords);
            DB::table('actions')->insert($newRecords);
        }

        return response()->json(['message' => 'Importation terminée.'], 200);
    }

    public function notifications(Request $request)
    {
        $filterEnCours = $request->query('filter_en_cours', 'all');
        $filterEnRetard = $request->query('filter_en_retard', 'all');
        $perPage = $request->query('per_page', 10);
        $pageEnCours = $request->query('page_en_cours', 1);
        $pageEnRetard = $request->query('page_en_retard', 1);
        $userId = $request->query('user_id'); // Récupérer l'userId depuis la requête

        // Query pour "En cours"
        $queryEnCours = Actions::join('sources', 'actions.sources_id', '=', 'sources.id')
        ->join('users', 'actions.users_id', '=', 'users.id')
        ->where('actions.statut', 'En cours');

        // Query pour "En retard"
        $queryEnRetard = Actions::join('sources', 'actions.sources_id', '=', 'sources.id')
        ->join('users', 'actions.users_id', '=', 'users.id')
        ->where('actions.statut', 'En retard');

        // Filtrer par userId si disponible
        if ($userId) {
            $queryEnCours->where('actions.users_id', $userId);
            $queryEnRetard->where('actions.users_id', $userId);
        }

        // Sélection des colonnes
        $queryEnCours->select(
            'actions.id',
            'actions.num_actions',
            'actions.description',
            'actions.frequence',
            'actions.statut',
            'sources.libelle as source_libelle',
            'users.id as users_id'
         )->orderBy('actions.id', 'desc');

        $queryEnRetard->select(
            'actions.id',
            'actions.num_actions',
            'actions.description',
            'actions.frequence',
            'actions.statut',
            'sources.libelle as source_libelle',
            'users.id as users_id'
         )->orderBy('actions.id', 'desc');

        // Appliquer les filtres
        if ($filterEnCours === 'AI') {
            $queryEnCours->where('actions.num_actions', 'like', 'AI-%');
        } elseif ($filterEnCours === 'PTA') {
            $queryEnCours->where('actions.num_actions', 'like', 'PTA-%');
        }

        if ($filterEnRetard === 'AI') {
            $queryEnRetard->where('actions.num_actions', 'like', 'AI-%');
        } elseif ($filterEnRetard === 'PTA') {
            $queryEnRetard->where('actions.num_actions', 'like', 'PTA-%');
        }

        // Paginer les résultats
        $totalEnCours = $queryEnCours->paginate($perPage, ['*'], 'page_en_cours', $pageEnCours);
        $totalEnRetard = $queryEnRetard->paginate($perPage, ['*'], 'page_en_retard', $pageEnRetard);

        return response()->json([
            'en_cours' => [
                'data' => $totalEnCours->items(),
                'total' => $totalEnCours->total(),
                'last_page' => $totalEnCours->lastPage(),
                'current_page' => $totalEnCours->currentPage(),
            ],
            'en_retard' => [
                'data' => $totalEnRetard->items(),
                'total' => $totalEnRetard->total(),
                'last_page' => $totalEnRetard->lastPage(),
                'current_page' => $totalEnRetard->currentPage(),
            ],
        ]);
    }

    public function getAllNotifications(Request $request)
    {
        $filterEnCours = $request->query('filter_en_cours', 'all');
        $filterEnRetard = $request->query('filter_en_retard', 'all');
        $userId = $request->query('user_id');

        // Query pour "En cours" sans pagination
        $queryEnCours = Actions::join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->where('actions.statut', 'En cours');

        // Query pour "En retard" sans pagination
        $queryEnRetard = Actions::join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->where('actions.statut', 'En retard');

        if ($userId) {
            $queryEnCours->where('actions.users_id', $userId);
            $queryEnRetard->where('actions.users_id', $userId);
        }

        // Sélection des colonnes
        $queryEnCours->select(
            'actions.id',
            'actions.num_actions',
            'actions.description',
            'actions.frequence',
            'actions.statut',
            'sources.libelle as source_libelle',
            'users.id as users_id'
        )->orderBy('actions.id', 'desc');

        $queryEnRetard->select(
            'actions.id',
            'actions.num_actions',
            'actions.description',
            'actions.frequence',
            'actions.statut',
            'sources.libelle as source_libelle',
            'users.id as users_id'
        )->orderBy('actions.id', 'desc');

        // Appliquer les filtres
        if ($filterEnCours === 'AI') {
            $queryEnCours->where('actions.num_actions', 'like', 'AI-%');
        } elseif ($filterEnCours === 'PTA') {
            $queryEnCours->where('actions.num_actions', 'like', 'PTA-%');
        }

        if ($filterEnRetard === 'AI') {
            $queryEnRetard->where('actions.num_actions', 'like', 'AI-%');
        } elseif ($filterEnRetard === 'PTA') {
            $queryEnRetard->where('actions.num_actions', 'like', 'PTA-%');
        }

        // Récupérer toutes les données sans pagination
        $enCours = $queryEnCours->get();
        $enRetard = $queryEnRetard->get();

        return response()->json([
            'en_cours' => $enCours,
            'en_retard' => $enRetard
        ]);
    }
}
