<?php

namespace App\Http\Controllers;

use App\Models\Actions;
use App\Models\Constat;
use App\Models\Responsable;
use App\Models\Sources;
use App\Models\Suivi;
use App\Models\TypeActions;
use App\Models\User;
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
            ->join('responsables', 'actions.responsables_id', '=', 'responsables.id')
            ->join('suivis', 'actions.suivis_id', '=', 'suivis.id')
            ->where('actions.num_actions', 'like', 'AI-%') // Filtrer uniquement les num_actions commençant par AI-
            ->where(function ($query) use ($search) {
                $query->where('actions.num_actions', 'like', "%$search%")
                      ->orWhere('actions.date', 'like', "%$search%")
                      ->orWhere('actions.description', 'like', "%$search%")
                        ->orWhere('actions.observation', 'like', "%$search%")
                        ->orWhere('actions.mesure', 'like', "%$search%")
                        ->orWhere('sources.libelle', 'like', "%$search%")
                        ->orWhere('type_actions.libelle', 'like', "%$search%")
                        ->orWhere('responsables.libelle', 'like', "%$search%")
                        ->orWhere('suivis.nom', 'like', "%$search%")
                      ->orWhere('actions.statut', 'like', "%$search%")
                      ->orWhere('actions.frequence', 'like', "%$search%")
                      ->orWhere('users.nom_utilisateur', 'like', "%$search%")
                      ->orWhere('constats.libelle', 'like', "%$search%");
            })
            ->select(
                'actions.*',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle',
                'responsables.libelle as responsable_libelle',
                'suivis.nom as suivi_nom'
            )
            ->orderBy('actions.id', 'desc') // Trier par ordre décroissant
            ->paginate($perPage);

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
            ->join('responsables', 'actions.responsables_id', '=', 'responsables.id')
            ->join('suivis', 'actions.suivis_id', '=', 'suivis.id')
            ->where('actions.num_actions', 'like', 'PTA-%') // Filtrer uniquement les num_actions commençant par PTA-
            ->where(function ($query) use ($search) {
                $query->where('actions.num_actions', 'like', "%$search%")
                      ->orWhere('actions.date', 'like', "%$search%")
                      ->orWhere('actions.description', 'like', "%$search%")
                        ->orWhere('actions.observation', 'like', "%$search%")
                        ->orWhere('actions.mesure', 'like', "%$search%")
                        ->orWhere('sources.libelle', 'like', "%$search%")
                        ->orWhere('type_actions.libelle', 'like', "%$search%")
                        ->orWhere('responsables.libelle', 'like', "%$search%")
                        ->orWhere('suivis.nom', 'like', "%$search%")
                      ->orWhere('actions.statut', 'like', "%$search%")
                      ->orWhere('actions.frequence', 'like', "%$search%")
                      ->orWhere('users.nom_utilisateur', 'like', "%$search%")
                      ->orWhere('constats.libelle', 'like', "%$search%");
            })
            ->select(
                'actions.*',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle',
                'responsables.libelle as responsable_libelle',
                'suivis.nom as suivi_nom'
            )
            ->orderBy('actions.id', 'desc') // Trier par ordre décroissant
            ->paginate($perPage);

    //         $actionsForLog = $actionsAI->toArray();

    //   FacadesLog::info('Actions AI:',$actionsForLog);

        return response()->json($actionsPTA);
    }
    public function show($id)
    {
        $action = Actions::join('users', 'actions.users_id', '=', 'users.id')
        ->join('constats', 'actions.constats_id', '=', 'constats.id')
        ->join('sources', 'actions.sources_id', '=', 'sources.id')
        ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
        ->join('responsables', 'actions.responsables_id', '=', 'responsables.id')
        ->join('suivis', 'actions.suivis_id', '=', 'suivis.id')
        ->where('actions.id', $id) // Filtrer par ID
        ->select(
            'actions.*',
            'users.nom_utilisateur as nom_utilisateur',
            'constats.libelle as constat_libelle',
            'sources.libelle as source_libelle',
            'type_actions.libelle as type_action_libelle',
            'responsables.libelle as responsable_libelle',
            'suivis.nom as suivi_nom'
        )
        ->first(); // Récupérer un seul enregistrement

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
            'sources_id' => $request->sources_id,
            'type_actions_id' => $request->type_actions_id,
            'responsables_id' => $request->responsables_id,
            'suivis_id' => $request->suivis_id,
            'constats_id' => $request->constats_id,
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
             $responsableId = Responsable::where('libelle', $row['responsable_libelle'])->value('id');
             $suiviId = Suivi::where('nom', $row['suivi_nom'])->value('id');
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
                     'responsables_id' => $responsableId,
                     'suivis_id' => $suiviId,
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
