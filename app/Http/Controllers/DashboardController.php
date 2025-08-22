<?php

namespace App\Http\Controllers;

use App\Models\Actions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function indexPTA(Request $request)
    {
        $query = Actions::query();

        // Ajouter le filtre pour num_actions commençant par PTA-
        $query->where('num_actions', 'like', 'PTA-%');

        // Filtrer par utilisateur si spécifié
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('users_id', $request->user_id);
        }

        // Récupère toutes les actions (filtrage date fait après)
        $actions = $query->get();

        // Récupère les dates du JSON
        $filteredActions = $actions->filter(function ($action) use ($request) {
            $observations = json_decode($action->observation_par_suivi, true);
            if (!$observations || !is_array($observations))
                return false;

            // Prend la dernière date (ou la première selon besoin)
            $date = $observations[count($observations) - 1]['date'] ?? null;
            if (!$date)
                return false;

            $type = $request->input('type', '1');
            switch ($type) {
                case '1': // Jours
                    $dateDebut = $request->input('date_debut');
                    $dateFin = $request->input('date_fin', $dateDebut);
                    return $date >= $dateDebut && $date <= $dateFin;
                case '2': // Mois
                    $mois = $request->input('mois');
                    $annee = $request->input('annee');
                    $moisFin = $request->input('mois_fin', $mois);
                    $anneeFin = $request->input('annee_fin', $annee);
                    $dateStart = "$annee-" . str_pad($mois, 2, '0', STR_PAD_LEFT) . "-01";
                    $dateEnd = "$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-01"));
                    return $date >= $dateStart && $date <= $dateEnd;
                case '3': // Ans
                    $annee = $request->input('annee');
                    $anneeFin = $request->input('annee_fin', $annee);
                    return substr($date, 0, 4) >= $annee && substr($date, 0, 4) <= $anneeFin;
            }
            return true;
        });

        // Groupement par type de constat
        $resultats = $filteredActions->groupBy(function ($action) {
            return $action->constat->libelle ?? 'Inconnu';
        })->map(function ($group) {
            return count($group);
        });

        $total = $resultats->sum();

        $statistiques = $resultats->map(function ($nombre, $libelle) use ($total) {
            $pourcentage = $total > 0 ? ($nombre / $total) * 100 : 0;
            return [
                'libelle' => $libelle,
                'nombre' => (int) $nombre,
                'pourcentage' => $pourcentage
            ];
        })->values();

        return response()->json($statistiques);
    }

    public function indexAI(Request $request)
    {
        $query = Actions::query();

        $query->where('num_actions', 'like', 'AI-%');

        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('users_id', $request->user_id);
        }

        // Récupère toutes les actions (filtrage date fait après)
        $actions = $query->get();

        // Récupère les dates du JSON
        $filteredActions = $actions->filter(function ($action) use ($request) {
            $observations = json_decode($action->observation_par_suivi, true);
            if (!$observations || !is_array($observations))
                return false;

            // Prend la dernière date (ou la première selon besoin)
            $date = $observations[count($observations) - 1]['date'] ?? null;
            if (!$date)
                return false;

            $type = $request->input('type', '1');
            switch ($type) {
                case '1': // Jours
                    $dateDebut = $request->input('date_debut');
                    $dateFin = $request->input('date_fin', $dateDebut);
                    return $date >= $dateDebut && $date <= $dateFin;
                case '2': // Mois
                    $mois = $request->input('mois');
                    $annee = $request->input('annee');
                    $moisFin = $request->input('mois_fin', $mois);
                    $anneeFin = $request->input('annee_fin', $annee);
                    $dateStart = "$annee-" . str_pad($mois, 2, '0', STR_PAD_LEFT) . "-01";
                    $dateEnd = "$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-01"));
                    return $date >= $dateStart && $date <= $dateEnd;
                case '3': // Ans
                    $annee = $request->input('annee');
                    $anneeFin = $request->input('annee_fin', $annee);
                    return substr($date, 0, 4) >= $annee && substr($date, 0, 4) <= $anneeFin;
            }
            return true;
        });

        // Groupement par type de constat
        $resultats = $filteredActions->groupBy(function ($action) {
            return $action->constat->libelle ?? 'Inconnu';
        })->map(function ($group) {
            return count($group);
        });

        $total = $resultats->sum();

        $statistiques = $resultats->map(function ($nombre, $libelle) use ($total) {
            $pourcentage = $total > 0 ? ($nombre / $total) * 100 : 0;
            return [
                'libelle' => $libelle,
                'nombre' => (int) $nombre,
                'pourcentage' => $pourcentage
            ];
        })->values();

        return response()->json($statistiques);
    }

    public function indexAE(Request $request)
    {
        $query = Actions::query();

        // Ajouter le filtre pour num_actions commençant par AE-
        $query->where('num_actions', 'like', 'AE-%');

        // Filtrer par utilisateur si spécifié
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('users_id', $request->user_id);
        }

        // Récupère toutes les actions (filtrage date fait après)
        $actions = $query->get();

        // Récupère les dates du JSON
        $filteredActions = $actions->filter(function ($action) use ($request) {
            $observations = json_decode($action->observation_par_suivi, true);
            if (!$observations || !is_array($observations))
                return false;

            // Prend la dernière date (ou la première selon besoin)
            $date = $observations[count($observations) - 1]['date'] ?? null;
            if (!$date)
                return false;

            $type = $request->input('type', '1');
            switch ($type) {
                case '1': // Jours
                    $dateDebut = $request->input('date_debut');
                    $dateFin = $request->input('date_fin', $dateDebut);
                    return $date >= $dateDebut && $date <= $dateFin;
                case '2': // Mois
                    $mois = $request->input('mois');
                    $annee = $request->input('annee');
                    $moisFin = $request->input('mois_fin', $mois);
                    $anneeFin = $request->input('annee_fin', $annee);
                    $dateStart = "$annee-" . str_pad($mois, 2, '0', STR_PAD_LEFT) . "-01";
                    $dateEnd = "$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-01"));
                    return $date >= $dateStart && $date <= $dateEnd;
                case '3': // Ans
                    $annee = $request->input('annee');
                    $anneeFin = $request->input('annee_fin', $annee);
                    return substr($date, 0, 4) >= $annee && substr($date, 0, 4) <= $anneeFin;
            }
            return true;
        });

        // Groupement par type de constat
        $resultats = $filteredActions->groupBy(function ($action) {
            return $action->constat->libelle ?? 'Inconnu';
        })->map(function ($group) {
            return count($group);
        });

        $total = $resultats->sum();

        $statistiques = $resultats->map(function ($nombre, $libelle) use ($total) {
            $pourcentage = $total > 0 ? ($nombre / $total) * 100 : 0;
            return [
                'libelle' => $libelle,
                'nombre' => (int) $nombre,
                'pourcentage' => $pourcentage
            ];
        })->values();

        return response()->json($statistiques);
    }

    public function indexSWOT(Request $request)
    {
        $query = Actions::query();

        // Ajouter le filtre pour num_actions commençant par SWOT-
        $query->where('num_actions', 'like', 'SWOT-%');

        // Filtrer par utilisateur si spécifié
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('users_id', $request->user_id);
        }

        // Récupère toutes les actions (filtrage date fait après)
        $actions = $query->get();

        // Récupère les dates du JSON
        $filteredActions = $actions->filter(function ($action) use ($request) {
            $observations = json_decode($action->observation_par_suivi, true);
            if (!$observations || !is_array($observations))
                return false;

            // Prend la dernière date (ou la première selon besoin)
            $date = $observations[count($observations) - 1]['date'] ?? null;
            if (!$date)
                return false;

            $type = $request->input('type', '1');
            switch ($type) {
                case '1': // Jours
                    $dateDebut = $request->input('date_debut');
                    $dateFin = $request->input('date_fin', $dateDebut);
                    return $date >= $dateDebut && $date <= $dateFin;
                case '2': // Mois
                    $mois = $request->input('mois');
                    $annee = $request->input('annee');
                    $moisFin = $request->input('mois_fin', $mois);
                    $anneeFin = $request->input('annee_fin', $annee);
                    $dateStart = "$annee-" . str_pad($mois, 2, '0', STR_PAD_LEFT) . "-01";
                    $dateEnd = "$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-01"));
                    return $date >= $dateStart && $date <= $dateEnd;
                case '3': // Ans
                    $annee = $request->input('annee');
                    $anneeFin = $request->input('annee_fin', $annee);
                    return substr($date, 0, 4) >= $annee && substr($date, 0, 4) <= $anneeFin;
            }
            return true;
        });

        // Groupement par type de constat
        $resultats = $filteredActions->groupBy(function ($action) {
            return $action->constat->libelle ?? 'Inconnu';
        })->map(function ($group) {
            return count($group);
        });

        $total = $resultats->sum();

        $statistiques = $resultats->map(function ($nombre, $libelle) use ($total) {
            $pourcentage = $total > 0 ? ($nombre / $total) * 100 : 0;
            return [
                'libelle' => $libelle,
                'nombre' => (int) $nombre,
                'pourcentage' => $pourcentage
            ];
        })->values();

        return response()->json($statistiques);
    }

    public function indexCAC(Request $request)
    {
        $query = Actions::query();

        // Ajouter le filtre pour num_actions commençant par CAC-
        $query->where('num_actions', 'like', 'CAC-%');

        // Filtrer par utilisateur si spécifié
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('users_id', $request->user_id);
        }

        // Récupère toutes les actions (filtrage date fait après)
        $actions = $query->get();

        // Récupère les dates du JSON
        $filteredActions = $actions->filter(function ($action) use ($request) {
            $observations = json_decode($action->observation_par_suivi, true);
            if (!$observations || !is_array($observations))
                return false;

            // Prend la dernière date (ou la première selon besoin)
            $date = $observations[count($observations) - 1]['date'] ?? null;
            if (!$date)
                return false;

            $type = $request->input('type', '1');
            switch ($type) {
                case '1': // Jours
                    $dateDebut = $request->input('date_debut');
                    $dateFin = $request->input('date_fin', $dateDebut);
                    return $date >= $dateDebut && $date <= $dateFin;
                case '2': // Mois
                    $mois = $request->input('mois');
                    $annee = $request->input('annee');
                    $moisFin = $request->input('mois_fin', $mois);
                    $anneeFin = $request->input('annee_fin', $annee);
                    $dateStart = "$annee-" . str_pad($mois, 2, '0', STR_PAD_LEFT) . "-01";
                    $dateEnd = "$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-01"));
                    return $date >= $dateStart && $date <= $dateEnd;
                case '3': // Ans
                    $annee = $request->input('annee');
                    $anneeFin = $request->input('annee_fin', $annee);
                    return substr($date, 0, 4) >= $annee && substr($date, 0, 4) <= $anneeFin;
            }
            return true;
        });

        // Groupement par type de constat
        $resultats = $filteredActions->groupBy(function ($action) {
            return $action->constat->libelle ?? 'Inconnu';
        })->map(function ($group) {
            return count($group);
        });

        $total = $resultats->sum();

        $statistiques = $resultats->map(function ($nombre, $libelle) use ($total) {
            $pourcentage = $total > 0 ? ($nombre / $total) * 100 : 0;
            return [
                'libelle' => $libelle,
                'nombre' => (int) $nombre,
                'pourcentage' => $pourcentage
            ];
        })->values();

        return response()->json($statistiques);
    }

    public function indexES(Request $request)
    {
        $query = Actions::query();

        // Ajouter le filtre pour num_actions commençant par ES-
        $query->where('num_actions', 'like', 'ES-%');

        // Filtrer par utilisateur si spécifié
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('users_id', $request->user_id);
        }

        // Récupère toutes les actions (filtrage date fait après)
        $actions = $query->get();

        // Récupère les dates du JSON
        $filteredActions = $actions->filter(function ($action) use ($request) {
            $observations = json_decode($action->observation_par_suivi, true);
            if (!$observations || !is_array($observations))
                return false;

            // Prend la dernière date (ou la première selon besoin)
            $date = $observations[count($observations) - 1]['date'] ?? null;
            if (!$date)
                return false;

            $type = $request->input('type', '1');
            switch ($type) {
                case '1': // Jours
                    $dateDebut = $request->input('date_debut');
                    $dateFin = $request->input('date_fin', $dateDebut);
                    return $date >= $dateDebut && $date <= $dateFin;
                case '2': // Mois
                    $mois = $request->input('mois');
                    $annee = $request->input('annee');
                    $moisFin = $request->input('mois_fin', $mois);
                    $anneeFin = $request->input('annee_fin', $annee);
                    $dateStart = "$annee-" . str_pad($mois, 2, '0', STR_PAD_LEFT) . "-01";
                    $dateEnd = "$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-" . date('t', strtotime("$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-01"));
                    return $date >= $dateStart && $date <= $dateEnd;
                case '3': // Ans
                    $annee = $request->input('annee');
                    $anneeFin = $request->input('annee_fin', $annee);
                    return substr($date, 0, 4) >= $annee && substr($date, 0, 4) <= $anneeFin;
            }
            return true;
        });

        // Groupement par type de constat
        $resultats = $filteredActions->groupBy(function ($action) {
            return $action->constat->libelle ?? 'Inconnu';
        })->map(function ($group) {
            return count($group);
        });

        $total = $resultats->sum();

        $statistiques = $resultats->map(function ($nombre, $libelle) use ($total) {
            $pourcentage = $total > 0 ? ($nombre / $total) * 100 : 0;
            return [
                'libelle' => $libelle,
                'nombre' => (int) $nombre,
                'pourcentage' => $pourcentage
            ];
        })->values();

        return response()->json($statistiques);
    }
    /**
     * Récupérer la liste des utilisateurs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        $users = User::select('id', 'nom_utilisateur')
            ->where('role', '!=', 'responsable') // Exclure les responsables
            ->where('role', '!=', 'suivi') // Exclure les responsables
            ->get();
        return response()->json($users);
    }

    public function getStats(Request $request)
    {
        $prefix = $request->prefix ?? 'AI'; // AI par défaut

        $stats = [
            'en_cours' => Actions::where('statut', 'En cours')
                ->where('num_actions', 'like', $prefix . '-%')
                ->count(),
            'en_retard' => Actions::where('statut', 'En retard')
                ->where('num_actions', 'like', $prefix . '-%')
                ->count(),
            'cloture' => Actions::where('statut', 'Clôturé')
                ->where('num_actions', 'like', $prefix . '-%')
                ->count(),
            'abandonne' => Actions::where('statut', 'Abandonné')
                ->where('num_actions', 'like', $prefix . '-%')
                ->count(),
            'regle' => Actions::where('statut', 'Réglé')
                ->where('num_actions', 'like', $prefix . '-%')
                ->count(),
            'non_regle' => Actions::where('statut', 'Non Réglé')
                ->where('num_actions', 'like', $prefix . '-%')
                ->count(),
            'non_realise' => Actions::where('statut', 'Non Réalisé')
                ->where('num_actions', 'like', $prefix . '-%')
                ->count(),
            'a_reporter' => Actions::where('statut', 'A Reporter')
                ->where('num_actions', 'like', $prefix . '-%')
                ->count(),
        ];

        return response()->json($stats);
    }

    public function getAIActionsByStatus(Request $request)
    {
        $statut = $request->statut;

        $query = Actions::where('statut', $statut)
            ->where('num_actions', 'like', 'AI-%')
            ->select('id', 'description', 'num_actions', 'statut', 'users_id')
            ->orderBy('id', 'desc');

        $actions = $query->get();

        return response()->json([
            'actions' => $actions,
            'total' => Actions::where('statut', $statut)
                ->where('num_actions', 'like', 'AI-%')
                ->count()
        ]);
    }

    public function getPTAActionsByStatus(Request $request)
    {
        $statut = $request->statut;

        $query = Actions::where('statut', $statut)
            ->where('num_actions', 'like', 'PTA-%')
            ->select('id', 'description', 'num_actions', 'statut', 'users_id')
            ->orderBy('id', 'desc');

        $actions = $query->get();

        return response()->json([
            'actions' => $actions,
            'total' => Actions::where('statut', $statut)
                ->where('num_actions', 'like', 'PTA-%')
                ->count()
        ]);
    }

    public function getCACActionsByStatus(Request $request)
    {
        $statut = $request->statut;

        $query = Actions::where('statut', $statut)
            ->where('num_actions', 'like', 'CAC-%')
            ->select('id', 'description', 'num_actions', 'statut', 'users_id')
            ->orderBy('id', 'desc');

        $actions = $query->get();

        return response()->json([
            'actions' => $actions,
            'total' => Actions::where('statut', $statut)
                ->where('num_actions', 'like', 'CAC-%')
                ->count()
        ]);
    }

    public function getAEActionsByStatus(Request $request)
    {
        $statut = $request->statut;

        $query = Actions::where('statut', $statut)
            ->where('num_actions', 'like', 'AE-%')
            ->select('id', 'description', 'num_actions', 'statut', 'users_id')
            ->orderBy('id', 'desc');

        $actions = $query->get();

        return response()->json([
            'actions' => $actions,
            'total' => Actions::where('statut', $statut)
                ->where('num_actions', 'like', 'AE-%')
                ->count()
        ]);
    }

    public function getSWOTActionsByStatus(Request $request)
    {
        $statut = $request->statut;

        $query = Actions::where('statut', $statut)
            ->where('num_actions', 'like', 'SWOT-%')
            ->select('id', 'description', 'num_actions', 'statut', 'users_id')
            ->orderBy('id', 'desc');

        $actions = $query->get();

        return response()->json([
            'actions' => $actions,
            'total' => Actions::where('statut', $statut)
                ->where('num_actions', 'like', 'SWOT-%')
                ->count()
        ]);
    }

    public function getESActionsByStatus(Request $request)
    {
        $statut = $request->statut;

        $query = Actions::where('statut', $statut)
            ->where('num_actions', 'like', 'ES-%')
            ->select('id', 'description', 'num_actions', 'statut', 'users_id')
            ->orderBy('id', 'desc');

        $actions = $query->get();

        return response()->json([
            'actions' => $actions,
            'total' => Actions::where('statut', $statut)
                ->where('num_actions', 'like', 'ES-%')
                ->count()
        ]);
    }
}
