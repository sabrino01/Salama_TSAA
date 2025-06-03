<?php

namespace App\Http\Controllers;

use App\Services\EmailSuivi;
use App\Models\Responsable;
use App\Models\Suivi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActionsSuiviController extends Controller
{
    public function indexSuiviAI(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // affichage 10 résultats par page
        $suivisId = $request->query('suivi_id'); // ID du suivis connecté

        $actionsSuivisAI = DB::table('actions_suivis')
            ->join('actions', 'actions_suivis.actions_id', '=', 'actions.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.num_actions', 'like', 'AI-%')
            ->when($suivisId, function ($query, $suivisId) {
                // Filtrer par suivis - correction ici
                return $query->where('actions_suivis.suivis_id', $suivisId);
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
                'actions_suivis.suivis_id',
                'actions_suivis.statut_suivi',
                'actions_suivis.observation_suivi',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle'
            )
            ->orderBy('actions.id', 'desc') // Trier par ordre décroissant
            ->paginate($perPage);

        return response()->json($actionsSuivisAI);
    }

    public function indexSuiviPTA(Request $request)
    {
        $search = $request->query('search', '');
        $perPage = $request->query('per_page', 10); // affichage 10 résultats par page
        $suivisId = $request->query('suivi_id'); // ID du suivis connecté

        $actionsSuivisPTA = DB::table('actions_suivis')
            ->join('actions', 'actions_suivis.actions_id', '=', 'actions.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.num_actions', 'like', 'PTA-%')
            ->when($suivisId, function ($query, $suivisId) {
                // Filtrer par suivis - correction ici
                return $query->where('actions_suivis.suivis_id', $suivisId);
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
                'actions_suivis.suivis_id',
                'actions_suivis.statut_suivi',
                'actions_suivis.observation_suivi',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle'
            )
            ->orderBy('actions.id', 'desc') // Trier par ordre décroissant
            ->paginate($perPage);

        return response()->json($actionsSuivisPTA);
    }

    public function showSuivi(Request $request, $id)
    {
        $suiviId = $request->query('suivi_id'); // ID du suivis connecté

        $actionSuivi = DB::table('actions_suivis')
            ->join('actions', 'actions_suivis.actions_id', '=', 'actions.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.id', $id)
            ->where('actions.num_actions', 'like', 'AI-%')
            ->when($suiviId, function ($query, $suiviId) {
                // Filtrer par suivis si l'ID est fourni
                return $query->where('actions_suivis.suivis_id', $suiviId);
            })
            ->select(
                'actions.*',
                'actions_suivis.suivis_id',
                'actions_suivis.statut_suivi',
                'actions_suivis.observation_suivi',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle'
            )
            ->first();

        if (!$actionSuivi) {
            return response()->json([
                'message' => 'Action non trouvée ou accès non autorisé'
            ], 404);
        }

        // Traitement des champs multiples : responsables_id et suivis_id
        $responsablesLibelle = null;
        $suivisLibelle = null;

        if ($actionSuivi->responsables_id) {
            $responsablesIds = explode(',', $actionSuivi->responsables_id);
            $responsablesLibelle = Responsable::whereIn('id', $responsablesIds)->pluck('libelle')->implode(', ');
        }

        if ($actionSuivi->suivis_id ?? false) {
            $suivisIds = explode(',', $actionSuivi->suivis_id);
            $suivisLibelle = Suivi::whereIn('id', $suivisIds)->pluck('nom')->implode(', ');
        }

        // Ajout des champs traités à la réponse
        $actionSuivi->responsable_libelle = $responsablesLibelle;
        $actionSuivi->suivi_nom = $suivisLibelle;

        return response()->json($actionSuivi);
    }

    public function showSuiviPTA(Request $request, $id)
    {
        $suiviId = $request->query('responsable_id'); // ID du suivis connecté

        $actionSuivi = DB::table('actions_suivis')
            ->join('actions', 'actions_suivis.actions_id', '=', 'actions.id')
            ->join('users', 'actions.users_id', '=', 'users.id')
            ->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->join('sources', 'actions.sources_id', '=', 'sources.id')
            ->join('type_actions', 'actions.type_actions_id', '=', 'type_actions.id')
            ->where('actions.id', $id)
            ->where('actions.num_actions', 'like', 'PTA-%')
            ->when($suiviId, function ($query, $suiviId) {
                // Filtrer par suivis si l'ID est fourni
                return $query->where('actions_suivis.suivis_id', $suiviId);
            })
            ->select(
                'actions.*',
                'actions_suivis.suivis_id',
                'actions_suivis.statut_suivi',
                'actions_suivis.observation_suivi',
                'users.nom_utilisateur as nom_utilisateur',
                'constats.libelle as constat_libelle',
                'sources.libelle as source_libelle',
                'type_actions.libelle as type_action_libelle'
            )
            ->first();

        if (!$actionSuivi) {
            return response()->json([
                'message' => 'Action non trouvée ou accès non autorisé'
            ], 404);
        }

        // Traitement des champs multiples : responsables_id et suivis_id
        $responsablesLibelle = null;
        $suivisLibelle = null;

        if ($actionSuivi->responsables_id) {
            $responsablesIds = explode(',', $actionSuivi->responsables_id);
            $responsablesLibelle = Responsable::whereIn('id', $responsablesIds)->pluck('libelle')->implode(', ');
        }

        if ($actionSuivi->suivis_id ?? false) {
            $suivisIds = explode(',', $actionSuivi->suivis_id);
            $suivisLibelle = Suivi::whereIn('id', $suivisIds)->pluck('nom')->implode(', ');
        }

        // Ajout des champs traités à la réponse
        $actionSuivi->responsable_libelle = $responsablesLibelle;
        $actionSuivi->suivi_nom = $suivisLibelle;

        return response()->json($actionSuivi);
    }

    // Fonction pour mettre à jour le statut et l'observation d'une suivis
    public function updateSuivi(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'statut_suivi' => 'required|string|in:En cours,En retard,Clôturé,Abandonné',
            'observation_suivi' => 'nullable|string|max:1000'
        ]);

        $suiviId = $request->query('suivi_id'); // ID du suivis connecté

        // Vérifier que l'ID du suivis est fourni
        if (!$suiviId) {
            return response()->json([
                'message' => 'ID du suivi requis'
            ], 400);
        }

        try {
            // Récupérer les informations de l'action et vérifier l'accès
            $actionInfo = DB::table('actions_suivis')
                ->join('actions', 'actions_suivis.actions_id', '=', 'actions.id')
                ->join('users', 'actions.users_id', '=', 'users.id')
                ->join('suivis', 'actions_suivis.suivis_id', '=', 'suivis.id')
                ->where('actions.id', $id)
                ->where('actions.num_actions', 'like', 'AI-%')
                ->where('actions_suivis.suivis_id', $suiviId)
                ->select(
                    'actions.id as action_id',
                    'actions.num_actions',
                    'actions.description as action_libelle',
                    'suivis.nom as suivi_libelle',
                    'suivis.email as suivi_email',
                    'users.email as user_email'
                )
                ->first();

            if (!$actionInfo) {
                return response()->json([
                    'message' => 'Action non trouvée ou accès non autorisé'
                ], 404);
            }

            // Mettre à jour les données dans actions_suivis
            $updated = DB::table('actions_suivis')
                ->where('actions_id', $id)
                ->where('suivis_id', $suiviId)
                ->update([
                    'statut_suivi' => $request->statut_suivi,
                    'observation_suivi' => $request->observation_suivi,
                    'updated_at' => now()
                ]);

            if ($updated) {
                // Récupérer l'ID de l'utilisateur lié à ce suivi
                $suiviUser = DB::table('users')
                    ->where('suivis_id', $suiviId)
                    ->select('id')
                    ->first();

                // Envoyer l'email via le service
                $emailService = new EmailSuivi();
                $emailEnvoye = $emailService->envoyerNotificationMiseAJourSuivi(
                    $actionInfo,
                    $request->statut_suivi,
                    $request->observation_suivi,
                    $suiviUser ? $suiviUser->id : null
                );

                $message = 'Suivi mise à jour avec succès';
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

    public function updateSuiviPTA(Request $request, $id)
    {
        // Validation des données
        $request->validate([
            'statut_suivi' => 'required|string|in:En cours,En retard,Clôturé,Abandonné',
            'observation_suivi' => 'nullable|string|max:1000'
        ]);

        $suiviId = $request->query('suivi_id'); // ID du suivi connecté

        // Vérifier que l'ID du suivi est fourni
        if (!$suiviId) {
            return response()->json([
                'message' => 'ID du suivi requis'
            ], 400);
        }

        try {
            // Récupérer les informations de l'action et vérifier l'accès
            $actionInfo = DB::table('actions_suivis')
                ->join('actions', 'actions_suivis.actions_id', '=', 'actions.id')
                ->join('users', 'actions.users_id', '=', 'users.id')
                ->join('suivis', 'actions_suivis.suivis_id', '=', 'suivis.id')
                ->where('actions.id', $id)
                ->where('actions.num_actions', 'like', 'PTA-%')
                ->where('actions_suivis.suivis_id', $suiviId)
                ->select(
                    'actions.id as action_id',
                    'actions.num_actions',
                    'actions.description as action_libelle',
                    'suivis.nom as suivi_libelle',
                    'suivis.email as suivi_email',
                    'users.email as user_email'
                )
                ->first();

            if (!$actionInfo) {
                return response()->json([
                    'message' => 'Action non trouvée ou accès non autorisé'
                ], 404);
            }

            // Mettre à jour les données dans actions_suivis
            $updated = DB::table('actions_suivis')
                ->where('actions_id', $id)
                ->where('suivis_id', $suiviId)
                ->update([
                    'statut_suivi' => $request->statut_suivi,
                    'observation_suivi' => $request->observation_suivi,
                    'updated_at' => now()
                ]);

            if ($updated) {
                // Récupérer l'ID de l'utilisateur lié à ce suivi
                $suiviUser = DB::table('users')
                    ->where('suivis_id', $suiviId)
                    ->select('id')
                    ->first();

                // Envoyer l'email via le service
                $emailService = new EmailSuivi();
                $emailEnvoye = $emailService->envoyerNotificationMiseAJourSuivi(
                    $actionInfo,
                    $request->statut_suivi,
                    $request->observation_suivi,
                    $suiviUser ? $suiviUser->id : null
                );

                $message = 'Suivi mise à jour avec succès';
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
