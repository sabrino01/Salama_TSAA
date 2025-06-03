<?php

namespace App\Http\Controllers;

use App\Services\EmailResponsable;
use Illuminate\Http\Request;
use App\Models\Responsable;
use App\Models\Suivi;
use Illuminate\Support\Facades\DB;

class ActionsResponsableController extends Controller
{
    public function indexResponsablesAI(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // affichage 10 résultats par page
        $responsableId = $request->query('responsable_id'); // ID du responsable connecté

        $actionsResponsablesAI = DB::table('actions_responsables')
            ->join('actions', 'actions_responsables.actions_id', '=', 'actions.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.num_actions', 'like', 'AI-%')
            ->when($responsableId, function ($query, $responsableId) {
                // Filtrer par responsable - correction ici
                return $query->where('actions_responsables.responsables_id', $responsableId);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('actions.num_actions', 'like', "%{$search}%")
                        ->orWhere('actions.date', 'like', "%{$search}%")
                        ->orWhere('actions.frequence', 'like', "%{$search}%")
                        ->orWhere('actions.description', 'like', "%{$search}%")
                        ->orWhere('sources.libelle', 'like', "%{$search}%")
                        ->orWhere('type_actions.libelle', 'like', "%{$search}%")
                        ->orWhere('constats.libelle', 'like', "%{$search}%")
                        ->orWhere('users.nom_utilisateur', 'like', "%{$search}%");
                });
            })
            ->select(
                'actions.*',
                'actions_responsables.responsables_id',
                'actions_responsables.statut_resp',
                'actions_responsables.observation_resp',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle'
            )
            ->orderBy('actions.id', 'desc') // Trier par ordre décroissant
            ->paginate($perPage);

        return response()->json($actionsResponsablesAI);
    }

    public function indexResponsablesPTA(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // affichage 10 résultats par page
        $responsableId = $request->query('responsable_id'); // ID du responsable connecté

        $actionsResponsablesPTA = DB::table('actions_responsables')
            ->join('actions', 'actions_responsables.actions_id', '=', 'actions.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.num_actions', 'like', 'PTA-%')
            ->when($responsableId, function ($query, $responsableId) {
                // Filtrer par responsable - correction ici
                return $query->where('actions_responsables.responsables_id', $responsableId);
            })
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('actions.num_actions', 'like', "%{$search}%")
                        ->orWhere('actions.date', 'like', "%{$search}%")
                        ->orWhere('actions.frequence', 'like', "%{$search}%")
                        ->orWhere('actions.description', 'like', "%{$search}%")
                        ->orWhere('sources.libelle', 'like', "%{$search}%")
                        ->orWhere('type_actions.libelle', 'like', "%{$search}%")
                        ->orWhere('constats.libelle', 'like', "%{$search}%")
                        ->orWhere('users.nom_utilisateur', 'like', "%{$search}%");
                });
            })
            ->select(
                'actions.*',
                'actions_responsables.responsables_id',
                'actions_responsables.statut_resp',
                'actions_responsables.observation_resp',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle'
            )
            ->orderBy('actions.id', 'desc') // Trier par ordre décroissant
            ->paginate($perPage);

        return response()->json($actionsResponsablesPTA);
    }

    // Fonction pour récupérer une action responsable spécifique
    public function showActionResponsable(Request $request, $id)
    {
        $responsableId = $request->query('responsable_id'); // ID du responsable connecté

        $actionResponsable = DB::table('actions_responsables')
            ->join('actions', 'actions_responsables.actions_id', '=', 'actions.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.id', $id)
            ->where('actions.num_actions', 'like', 'AI-%')
            ->when($responsableId, function ($query, $responsableId) {
                // Filtrer par responsable si l'ID est fourni
                return $query->where('actions_responsables.responsables_id', $responsableId);
            })
            ->select(
                'actions.*',
                'actions_responsables.responsables_id',
                'actions_responsables.statut_resp',
                'actions_responsables.observation_resp',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle',
            )
            ->first();

        if (!$actionResponsable) {
            return response()->json([
                'message' => 'Action non trouvée ou accès non autorisé'
            ], 404);
        }

        // Traitement des champs multiples : responsables_id et suivis_id
        $responsablesLibelle = null;
        $suivisLibelle = null;

        if ($actionResponsable->responsables_id) {
            $responsablesIds = explode(',', $actionResponsable->responsables_id);
            $responsablesLibelle = Responsable::whereIn('id', $responsablesIds)->pluck('libelle')->implode(', ');
        }

        if ($actionResponsable->suivis_id ?? false) {
            $suivisIds = explode(',', $actionResponsable->suivis_id);
            $suivisLibelle = Suivi::whereIn('id', $suivisIds)->pluck('nom')->implode(', ');
        }

        // Ajout des champs traités à la réponse
        $actionResponsable->responsable_libelle = $responsablesLibelle;
        $actionResponsable->suivi_nom = $suivisLibelle;

        return response()->json($actionResponsable);
    }

    public function showActionResponsablePTA(Request $request, $id)
    {
        $responsableId = $request->query('responsable_id'); // ID du responsable connecté

        $actionResponsable = DB::table('actions_responsables')
            ->join('actions', 'actions_responsables.actions_id', '=', 'actions.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.id', $id)
            ->where('actions.num_actions', 'like', 'PTA-%')
            ->when($responsableId, function ($query, $responsableId) {
                // Filtrer par responsable si l'ID est fourni
                return $query->where('actions_responsables.responsables_id', $responsableId);
            })
            ->select(
                'actions.*',
                'actions_responsables.responsables_id',
                'actions_responsables.statut_resp',
                'actions_responsables.observation_resp',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle'
            )
            ->first();

        if (!$actionResponsable) {
            return response()->json([
                'message' => 'Action non trouvée ou accès non autorisé'
            ], 404);
        }

        // Traitement des champs multiples : responsables_id et suivis_id
        $responsablesLibelle = null;
        $suivisLibelle = null;

        if ($actionResponsable->responsables_id) {
            $responsablesIds = explode(',', $actionResponsable->responsables_id);
            $responsablesLibelle = Responsable::whereIn('id', $responsablesIds)->pluck('libelle')->implode(', ');
        }

        if ($actionResponsable->suivis_id ?? false) {
            $suivisIds = explode(',', $actionResponsable->suivis_id);
            $suivisLibelle = Suivi::whereIn('id', $suivisIds)->pluck('nom')->implode(', ');
        }

        // Ajout des champs traités à la réponse
        $actionResponsable->responsable_libelle = $responsablesLibelle;
        $actionResponsable->suivi_nom = $suivisLibelle;

        return response()->json($actionResponsable);
    }

    // Fonction pour mettre à jour le statut et l'observation d'une action responsable
    public function updateActionResponsable(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'statut_resp' => 'required|string|in:En cours,En retard,Clôturé,Abandonné',
            'observation_resp' => 'nullable|string|max:1000'
        ]);

        $responsableId = $request->query('responsable_id');

        // Vérifier que l'ID du responsable est fourni
        if (!$responsableId) {
            return response()->json([
                'message' => 'ID du responsable requis'
            ], 400);
        }

        try {
            $actionInfo = DB::table('actions_responsables')
                ->join('actions', 'actions_responsables.actions_id', '=', 'actions.id')
                ->join('users', 'actions.users_id', '=', 'users.id')
                ->join('responsables', 'actions_responsables.responsables_id', '=', 'responsables.id')
                ->where('actions.id', $id)
                ->where('actions.num_actions', 'like', 'AI-%')
                ->where('actions_responsables.responsables_id', $responsableId)
                ->select(
                    'actions.id as action_id',
                    'actions.num_actions',
                    'actions.description as action_libelle',
                    'responsables.libelle as responsable_libelle',
                    'responsables.email as responsable_email',
                    'users.email as user_email'
                )
                ->first();


            if (!$actionInfo) {
                return response()->json([
                    'message' => 'Action non trouvée ou accès non autorisé'
                ], 404);
            }

            // Mettre à jour les données dans actions_responsables
            $updated = DB::table('actions_responsables')
                ->where('actions_id', $id)
                ->where('responsables_id', $responsableId)
                ->update([
                    'statut_resp' => $request->statut_resp,
                    'observation_resp' => $request->observation_resp,
                    'updated_at' => now()
                ]);

            if ($updated) {
                // Récupérer l'ID de l'utilisateur lié à ce responsable
                $responsableUser = DB::table('users')
                    ->where('responsables_id', $responsableId)
                    ->select('id')
                    ->first();

                // Envoyer l'email via le service
                $emailService = new EmailResponsable();
                $emailEnvoye = $emailService->envoyerNotificationMiseAJour(
                    $actionInfo,
                    $request->statut_resp,
                    $request->observation_resp,
                    $responsableUser ? $responsableUser->id : null
                );

                $message = 'Action responsable mise à jour avec succès';
                $response = ['message' => $message];

                if (!$emailEnvoye) {
                    $response['warning'] = 'Email de notification non envoyé';
                } else {
                    $response['message'] .= ' et notification envoyée';
                }

                return response()->json($response);

            } else {
                return response()->json([
                    'message' => 'Aucune modification effectuée'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateActionResponsablePTA(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'statut_resp' => 'required|string|in:En cours,En retard,Clôturé,Abandonné',
            'observation_resp' => 'nullable|string|max:1000'
        ]);

        $responsableId = $request->query('responsable_id'); // ID du responsable connecté

        // Vérifier que l'ID du responsable est fourni
        if (!$responsableId) {
            return response()->json([
                'message' => 'ID du responsable requis'
            ], 400);
        }

        try {
            $actionInfo = DB::table('actions_responsables')
                ->join('actions', 'actions_responsables.actions_id', '=', 'actions.id')
                ->join('users', 'actions.users_id', '=', 'users.id')
                ->join('responsables', 'actions_responsables.responsables_id', '=', 'responsables.id')
                ->where('actions.id', $id)
                ->where('actions.num_actions', 'like', 'PTA-%')
                ->where('actions_responsables.responsables_id', $responsableId)
                ->select(
                    'actions.id as action_id',
                    'actions.num_actions',
                    'actions.description as action_libelle',
                    'responsables.libelle as responsable_libelle',
                    'responsables.email as responsable_email',
                    'users.email as user_email'
                )
                ->first();


            if (!$actionInfo) {
                return response()->json([
                    'message' => 'Action non trouvée ou accès non autorisé'
                ], 404);
            }

            // Mettre à jour les données dans actions_responsables
            $updated = DB::table('actions_responsables')
                ->where('actions_id', $id)
                ->where('responsables_id', $responsableId)
                ->update([
                    'statut_resp' => $request->statut_resp,
                    'observation_resp' => $request->observation_resp,
                    'updated_at' => now()
                ]);

            if ($updated) {
                // Récupérer l'ID de l'utilisateur lié à ce responsable
                $responsableUser = DB::table('users')
                    ->where('responsables_id', $responsableId)
                    ->select('id')
                    ->first();

                // Envoyer l'email via le service
                $emailService = new EmailResponsable();
                $emailEnvoye = $emailService->envoyerNotificationMiseAJour(
                    $actionInfo,
                    $request->statut_resp,
                    $request->observation_resp,
                    $responsableUser ? $responsableUser->id : null
                );

                $message = 'Action responsable mise à jour avec succès';
                $response = ['message' => $message];

                if (!$emailEnvoye) {
                    $response['warning'] = 'Email de notification non envoyé';
                } else {
                    $response['message'] .= ' et notification envoyée';
                }

                return response()->json($response);

            } else {
                return response()->json([
                    'message' => 'Aucune modification effectuée'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
