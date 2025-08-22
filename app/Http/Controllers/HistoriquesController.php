<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actions;
use App\Models\Actions_responsable;
use App\Models\Actions_suivi;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\Auth;

class HistoriquesController extends Controller
{
    public function indexAI(Request $request)
    {
        $perPage = $request->query('per_page', 20); // 20 résultats par page par défaut

        // Récupérer toutes les actions avec le préfixe AI-
        $actionsAI = Actions::where('num_actions', 'LIKE', 'AI-%')
            ->with(['user', 'responsables', 'suivis'])
            ->get();

        $historique = collect();

        foreach ($actionsAI as $action) {
            // Récupérer les audits liés à cette action
            $actionAudits = Audit::where('auditable_type', Actions::class)
                ->where('auditable_id', $action->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($actionAudits as $audit) {
                $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');
                $userId = $audit->user_id ?? $action->users_id;

                $historique->push([
                    'id' => 'action_' . $audit->id,
                    'type' => $audit->event === 'created' ? 'creation' : 'modification',
                    'table' => 'actions',
                    'action_num' => $action->num_actions,
                    'action_description' => $action->description,
                    'user_name' => $userName,
                    'user_id' => $userId,
                    'event' => $audit->event,
                    'old_values' => $old,
                    'new_values' => $new,
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->created_at,
                    'modified_fields' => $audit->event === 'updated'
                        ? array_keys($new ?? [])
                        : []
                ]);
            }

            // Audits pour actions_responsables
            $responsableIds = Actions_responsable::where('actions_id', $action->id)->pluck('id');

            if ($responsableIds->isNotEmpty()) {
                $responsableAudits = Audit::where('auditable_type', Actions_responsable::class)
                    ->where('auditable_id', $responsableIds)
                    ->with(['user', 'auditable.responsable'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($responsableAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'responsable_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_responsable' : 'modification_responsable',
                        'table' => 'actions_responsables',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'responsable_name' => optional($audit->auditable->responsable)->libelle ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }

            // Audits pour actions_suivis
            $suiviIds = Actions_suivi::where('actions_id', $action->id)->pluck('id');

            if ($suiviIds->isNotEmpty()) {
                $suiviAudits = Audit::where('auditable_type', Actions_suivi::class)
                    ->whereIn('auditable_id', $suiviIds)
                    ->with(['user', 'auditable.suivi'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($suiviAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'suivi_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_suivi' : 'modification_suivi',
                        'table' => 'actions_suivis',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'suivi_name' => optional($audit->auditable->suivi)->nom ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }
        }

        // Trier par date décroissante
        $historique = $historique->sortByDesc('created_at')->values();

        // Appliquer la pagination manuelle
        $currentPage = $request->query('page', 1);
        $total = $historique->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = $historique->slice($offset, $perPage)->values();

        // Calculer les informations de pagination
        $lastPage = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => $paginatedData,
            'current_page' => (int) $currentPage,
            'per_page' => (int) $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]);
    }

    public function indexPTA(Request $request)
    {
        $perPage = $request->query('per_page', 20); // 20 résultats par page par défaut

        // Récupérer toutes les actions avec le préfixe PTA-
        $actionsPTA = Actions::where('num_actions', 'LIKE', 'PTA-%')
            ->with(['user', 'responsables', 'suivis'])
            ->get();

        $historique = collect();

        foreach ($actionsPTA as $action) {
            // Récupérer les audits liés à cette action
            $actionAudits = Audit::where('auditable_type', Actions::class)
                ->where('auditable_id', $action->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($actionAudits as $audit) {
                $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');
                $userId = $audit->user_id ?? $action->users_id;

                $historique->push([
                    'id' => 'action_' . $audit->id,
                    'type' => $audit->event === 'created' ? 'creation' : 'modification',
                    'table' => 'actions',
                    'action_num' => $action->num_actions,
                    'action_description' => $action->description,
                    'user_name' => $userName,
                    'user_id' => $userId,
                    'event' => $audit->event,
                    'old_values' => $old,
                    'new_values' => $new,
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->created_at,
                    'modified_fields' => $audit->event === 'updated'
                        ? array_keys($new ?? [])
                        : []
                ]);
            }

            // Audits pour actions_responsables
            $responsableIds = Actions_responsable::where('actions_id', $action->id)->pluck('id');

            if ($responsableIds->isNotEmpty()) {
                $responsableAudits = Audit::where('auditable_type', Actions_responsable::class)
                    ->where('auditable_id', $responsableIds)
                    ->with(['user', 'auditable.responsable'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($responsableAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'responsable_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_responsable' : 'modification_responsable',
                        'table' => 'actions_responsables',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'responsable_name' => optional($audit->auditable->responsable)->libelle ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }

            // Audits pour actions_suivis
            $suiviIds = Actions_suivi::where('actions_id', $action->id)->pluck('id');

            if ($suiviIds->isNotEmpty()) {
                $suiviAudits = Audit::where('auditable_type', Actions_suivi::class)
                    ->whereIn('auditable_id', $suiviIds)
                    ->with(['user', 'auditable.suivi'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($suiviAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'suivi_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_suivi' : 'modification_suivi',
                        'table' => 'actions_suivis',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'suivi_name' => optional($audit->auditable->suivi)->nom ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }
        }

        // Trier par date décroissante
        $historique = $historique->sortByDesc('created_at')->values();

        // Appliquer la pagination manuelle
        $currentPage = $request->query('page', 1);
        $total = $historique->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = $historique->slice($offset, $perPage)->values();

        // Calculer les informations de pagination
        $lastPage = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => $paginatedData,
            'current_page' => (int) $currentPage,
            'per_page' => (int) $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]);
    }

    public function indexCAC(Request $request)
    {
        $perPage = $request->query('per_page', 20); // 20 résultats par page par défaut

        // Récupérer toutes les actions avec le préfixe CAC-
        $actionsCAC = Actions::where('num_actions', 'LIKE', 'CAC-%')
            ->with(['user', 'responsables', 'suivis'])
            ->get();

        $historique = collect();

        foreach ($actionsCAC as $action) {
            // Récupérer les audits liés à cette action
            $actionAudits = Audit::where('auditable_type', Actions::class)
                ->where('auditable_id', $action->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($actionAudits as $audit) {
                $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');
                $userId = $audit->user_id ?? $action->users_id;

                $historique->push([
                    'id' => 'action_' . $audit->id,
                    'type' => $audit->event === 'created' ? 'creation' : 'modification',
                    'table' => 'actions',
                    'action_num' => $action->num_actions,
                    'action_description' => $action->description,
                    'user_name' => $userName,
                    'user_id' => $userId,
                    'event' => $audit->event,
                    'old_values' => $old,
                    'new_values' => $new,
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->created_at,
                    'modified_fields' => $audit->event === 'updated'
                        ? array_keys($new ?? [])
                        : []
                ]);
            }

            // Audits pour actions_responsables
            $responsableIds = Actions_responsable::where('actions_id', $action->id)->pluck('id');

            if ($responsableIds->isNotEmpty()) {
                $responsableAudits = Audit::where('auditable_type', Actions_responsable::class)
                    ->where('auditable_id', $responsableIds)
                    ->with(['user', 'auditable.responsable'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($responsableAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'responsable_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_responsable' : 'modification_responsable',
                        'table' => 'actions_responsables',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'responsable_name' => optional($audit->auditable->responsable)->libelle ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }

            // Audits pour actions_suivis
            $suiviIds = Actions_suivi::where('actions_id', $action->id)->pluck('id');

            if ($suiviIds->isNotEmpty()) {
                $suiviAudits = Audit::where('auditable_type', Actions_suivi::class)
                    ->whereIn('auditable_id', $suiviIds)
                    ->with(['user', 'auditable.suivi'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($suiviAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'suivi_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_suivi' : 'modification_suivi',
                        'table' => 'actions_suivis',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'suivi_name' => optional($audit->auditable->suivi)->nom ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }
        }

        // Trier par date décroissante
        $historique = $historique->sortByDesc('created_at')->values();

        // Appliquer la pagination manuelle
        $currentPage = $request->query('page', 1);
        $total = $historique->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = $historique->slice($offset, $perPage)->values();

        // Calculer les informations de pagination
        $lastPage = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => $paginatedData,
            'current_page' => (int) $currentPage,
            'per_page' => (int) $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]);
    }

    public function indexAE(Request $request)
    {
        $perPage = $request->query('per_page', 20); // 20 résultats par page par défaut

        // Récupérer toutes les actions avec le préfixe AE-
        $actionsAE = Actions::where('num_actions', 'LIKE', 'AE-%')
            ->with(['user', 'responsables', 'suivis'])
            ->get();

        $historique = collect();

        foreach ($actionsAE as $action) {
            // Récupérer les audits liés à cette action
            $actionAudits = Audit::where('auditable_type', Actions::class)
                ->where('auditable_id', $action->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($actionAudits as $audit) {
                $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');
                $userId = $audit->user_id ?? $action->users_id;

                $historique->push([
                    'id' => 'action_' . $audit->id,
                    'type' => $audit->event === 'created' ? 'creation' : 'modification',
                    'table' => 'actions',
                    'action_num' => $action->num_actions,
                    'action_description' => $action->description,
                    'user_name' => $userName,
                    'user_id' => $userId,
                    'event' => $audit->event,
                    'old_values' => $old,
                    'new_values' => $new,
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->created_at,
                    'modified_fields' => $audit->event === 'updated'
                        ? array_keys($new ?? [])
                        : []
                ]);
            }

            // Audits pour actions_responsables
            $responsableIds = Actions_responsable::where('actions_id', $action->id)->pluck('id');

            if ($responsableIds->isNotEmpty()) {
                $responsableAudits = Audit::where('auditable_type', Actions_responsable::class)
                    ->where('auditable_id', $responsableIds)
                    ->with(['user', 'auditable.responsable'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($responsableAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'responsable_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_responsable' : 'modification_responsable',
                        'table' => 'actions_responsables',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'responsable_name' => optional($audit->auditable->responsable)->libelle ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }

            // Audits pour actions_suivis
            $suiviIds = Actions_suivi::where('actions_id', $action->id)->pluck('id');

            if ($suiviIds->isNotEmpty()) {
                $suiviAudits = Audit::where('auditable_type', Actions_suivi::class)
                    ->whereIn('auditable_id', $suiviIds)
                    ->with(['user', 'auditable.suivi'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($suiviAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'suivi_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_suivi' : 'modification_suivi',
                        'table' => 'actions_suivis',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'suivi_name' => optional($audit->auditable->suivi)->nom ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }
        }

        // Trier par date décroissante
        $historique = $historique->sortByDesc('created_at')->values();

        // Appliquer la pagination manuelle
        $currentPage = $request->query('page', 1);
        $total = $historique->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = $historique->slice($offset, $perPage)->values();

        // Calculer les informations de pagination
        $lastPage = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => $paginatedData,
            'current_page' => (int) $currentPage,
            'per_page' => (int) $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]);
    }

    public function indexSWOT(Request $request)
    {
        $perPage = $request->query('per_page', 20); // 20 résultats par page par défaut

        // Récupérer toutes les actions avec le préfixe SWOT-
        $actionsSWOT = Actions::where('num_actions', 'LIKE', 'SWOT-%')
            ->with(['user', 'responsables', 'suivis'])
            ->get();

        $historique = collect();

        foreach ($actionsSWOT as $action) {
            // Récupérer les audits liés à cette action
            $actionAudits = Audit::where('auditable_type', Actions::class)
                ->where('auditable_id', $action->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($actionAudits as $audit) {
                $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');
                $userId = $audit->user_id ?? $action->users_id;

                $historique->push([
                    'id' => 'action_' . $audit->id,
                    'type' => $audit->event === 'created' ? 'creation' : 'modification',
                    'table' => 'actions',
                    'action_num' => $action->num_actions,
                    'action_description' => $action->description,
                    'user_name' => $userName,
                    'user_id' => $userId,
                    'event' => $audit->event,
                    'old_values' => $old,
                    'new_values' => $new,
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->created_at,
                    'modified_fields' => $audit->event === 'updated'
                        ? array_keys($new ?? [])
                        : []
                ]);
            }

            // Audits pour actions_responsables
            $responsableIds = Actions_responsable::where('actions_id', $action->id)->pluck('id');

            if ($responsableIds->isNotEmpty()) {
                $responsableAudits = Audit::where('auditable_type', Actions_responsable::class)
                    ->where('auditable_id', $responsableIds)
                    ->with(['user', 'auditable.responsable'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($responsableAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'responsable_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_responsable' : 'modification_responsable',
                        'table' => 'actions_responsables',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'responsable_name' => optional($audit->auditable->responsable)->libelle ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }

            // Audits pour actions_suivis
            $suiviIds = Actions_suivi::where('actions_id', $action->id)->pluck('id');

            if ($suiviIds->isNotEmpty()) {
                $suiviAudits = Audit::where('auditable_type', Actions_suivi::class)
                    ->whereIn('auditable_id', $suiviIds)
                    ->with(['user', 'auditable.suivi'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($suiviAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'suivi_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_suivi' : 'modification_suivi',
                        'table' => 'actions_suivis',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'suivi_name' => optional($audit->auditable->suivi)->nom ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }
        }

        // Trier par date décroissante
        $historique = $historique->sortByDesc('created_at')->values();

        // Appliquer la pagination manuelle
        $currentPage = $request->query('page', 1);
        $total = $historique->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = $historique->slice($offset, $perPage)->values();

        // Calculer les informations de pagination
        $lastPage = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => $paginatedData,
            'current_page' => (int) $currentPage,
            'per_page' => (int) $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]);
    }

    public function indexEnquete(Request $request)
    {
        $perPage = $request->query('per_page', 20); // 20 résultats par page par défaut

        // Récupérer toutes les actions avec le préfixe ES-
        $actionsES = Actions::where('num_actions', 'LIKE', 'ES-%')
            ->with(['user', 'responsables', 'suivis'])
            ->get();

        $historique = collect();

        foreach ($actionsES as $action) {
            // Récupérer les audits liés à cette action
            $actionAudits = Audit::where('auditable_type', Actions::class)
                ->where('auditable_id', $action->id)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($actionAudits as $audit) {
                $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');
                $userId = $audit->user_id ?? $action->users_id;

                $historique->push([
                    'id' => 'action_' . $audit->id,
                    'type' => $audit->event === 'created' ? 'creation' : 'modification',
                    'table' => 'actions',
                    'action_num' => $action->num_actions,
                    'action_description' => $action->description,
                    'user_name' => $userName,
                    'user_id' => $userId,
                    'event' => $audit->event,
                    'old_values' => $old,
                    'new_values' => $new,
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->created_at,
                    'modified_fields' => $audit->event === 'updated'
                        ? array_keys($new ?? [])
                        : []
                ]);
            }

            // Audits pour actions_responsables
            $responsableIds = Actions_responsable::where('actions_id', $action->id)->pluck('id');

            if ($responsableIds->isNotEmpty()) {
                $responsableAudits = Audit::where('auditable_type', Actions_responsable::class)
                    ->where('auditable_id', $responsableIds)
                    ->with(['user', 'auditable.responsable'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($responsableAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'responsable_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_responsable' : 'modification_responsable',
                        'table' => 'actions_responsables',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'responsable_name' => optional($audit->auditable->responsable)->libelle ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }

            // Audits pour actions_suivis
            $suiviIds = Actions_suivi::where('actions_id', $action->id)->pluck('id');

            if ($suiviIds->isNotEmpty()) {
                $suiviAudits = Audit::where('auditable_type', Actions_suivi::class)
                    ->whereIn('auditable_id', $suiviIds)
                    ->with(['user', 'auditable.suivi'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($suiviAudits as $audit) {
                    $old = is_string($audit->old_values) ? json_decode($audit->old_values, true) : $audit->old_values;
                    $new = is_string($audit->new_values) ? json_decode($audit->new_values, true) : $audit->new_values;

                    // Si l'audit n'a pas d'utilisateur, on récupère celui depuis la relation de l'action
                    $userName = $audit->user ? $audit->user->nom_utilisateur : ($action->user->nom_utilisateur ?? 'Système');

                    $historique->push([
                        'id' => 'suivi_' . $audit->id,
                        'type' => $audit->event === 'created' ? 'creation_suivi' : 'modification_suivi',
                        'table' => 'actions_suivis',
                        'action_num' => $action->num_actions,
                        'action_description' => $action->description,
                        'user_name' => $userName,
                        'suivi_name' => optional($audit->auditable->suivi)->nom ?? 'Inconnu',
                        'user_id' => $audit->user_id,
                        'event' => $audit->event,
                        'old_values' => $old,
                        'new_values' => $new,
                        'created_at' => $audit->created_at,
                        'updated_at' => $audit->created_at,
                        'modified_fields' => $audit->event === 'updated'
                            ? array_keys($new ?? [])
                            : []
                    ]);
                }
            }
        }

        // Trier par date décroissante
        $historique = $historique->sortByDesc('created_at')->values();

        // Appliquer la pagination manuelle
        $currentPage = $request->query('page', 1);
        $total = $historique->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = $historique->slice($offset, $perPage)->values();

        // Calculer les informations de pagination
        $lastPage = ceil($total / $perPage);

        return response()->json([
            'success' => true,
            'data' => $paginatedData,
            'current_page' => (int) $currentPage,
            'per_page' => (int) $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $offset + 1,
            'to' => min($offset + $perPage, $total)
        ]);
    }

    public function indexAII()
    {
        // Récupérer toutes les actions avec le préfixe AII-
        $actionsAII = Actions::where('num_actions', 'LIKE', 'AII-%')
            ->with(['user', 'responsables', 'suivis'])
            ->get();

        $historique = collect();

        foreach ($actionsAII as $action) {
            // Récupérer les audits liés à cette action
            $actionAudits = Audit::where('auditable_type', Actions::class)
                ->where('auditable_id', $action->id)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($actionAudits as $audit) {
                $historique->push([
                    'id' => 'action_' . $audit->id,
                    'type' => $audit->event === 'created' ? 'creation' : 'modification',
                    'table' => 'actions',
                    'action_num' => $action->num_actions,
                    'action_description' => $action->description,
                    'user_name' => optional($audit->user)->nom_utilisateur ?? 'Système',
                    'user_id' => $audit->user_id,
                    'event' => $audit->event,
                    'old_values' => json_decode($audit->old_values, true),
                    'new_values' => json_decode($audit->new_values, true),
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->created_at,
                    'modified_fields' => $audit->event === 'updated'
                        ? array_keys(json_decode($audit->new_values ?? '{}', true))
                        : []
                ]);
            }
        }

        // Audits pour actions_responsables
        $responsableIds = Actions_responsable::where('actions_id', $action->id)->pluck('id');

        if ($responsableIds->isNotEmpty()) {
            $responsableAudits = Audit::where('auditable_type', Actions_responsable::class)
                ->whereIn('auditable_id', $responsableIds)
                ->with(['user', 'auditable.responsable'])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($responsableAudits as $audit) {
                $historique->push([
                    'id' => 'responsable_' . $audit->id,
                    'type' => 'modification_responsable',
                    'table' => 'actions_responsables',
                    'action_num' => $action->num_actions,
                    'action_description' => $action->description,
                    'user_name' => $audit->user ? $audit->user->nom_utilisateur : 'Système',
                    'responsable_name' => optional($audit->auditable->responsable)->libelle ?? 'Inconnu',
                    'user_id' => $audit->user_id,
                    'event' => $audit->event,
                    'old_values' => $audit->old_values,
                    'new_values' => $audit->new_values,
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->created_at,
                    'modified_fields' => array_keys($audit->new_values ?? [])
                ]);
            }
        }

        // Audits pour actions_suivis
        $suiviIds = Actions_suivi::where('actions_id', $action->id)->pluck('id');

        if ($suiviIds->isNotEmpty()) {
            $suiviAudits = Audit::where('auditable_type', Actions_suivi::class)
                ->whereIn('auditable_id', $suiviIds)
                ->with(['user', 'auditable.suivi'])
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($suiviAudits as $audit) {
                $historique->push([
                    'id' => 'suivi_' . $audit->id,
                    'type' => 'modification_suivi',
                    'table' => 'actions_suivis',
                    'action_num' => $action->num_actions,
                    'action_description' => $action->description,
                    'user_name' => $audit->user ? $audit->user->nom_utilisateur : 'Système',
                    'suivi_name' => optional($audit->auditable->suivi)->nom ?? 'Inconnu',
                    'user_id' => $audit->user_id,
                    'event' => $audit->event,
                    'old_values' => $audit->old_values,
                    'new_values' => $audit->new_values,
                    'created_at' => $audit->created_at,
                    'updated_at' => $audit->created_at,
                    'modified_fields' => array_keys($audit->new_values ?? [])
                ]);
            }
        }

        // Trier par date décroissante
        $historique = $historique->sortByDesc('created_at')->values();

        return response()->json([
            'success' => true,
            'data' => $historique
        ]);
    }

    public function show($id)
    {
        // Récupérer les détails d'un audit spécifique
        $auditId = str_replace(['action_', 'responsable_', 'suivi_'], '', $id);
        $audit = Audit::with(['user', 'auditable'])->find($auditId);

        if (!$audit) {
            return response()->json([
                'success' => false,
                'message' => 'Audit non trouvé'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $audit
        ]);
    }
}
