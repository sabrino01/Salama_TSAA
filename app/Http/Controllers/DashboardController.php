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

        // Appliquer les filtres de date selon le type de recherche
        $type = $request->input('type', '1');

        switch ($type) {
            case '1': // Jours
                $dateDebut = $request->input('date_debut');
                $dateFin = $request->input('date_fin', $dateDebut);

                $query->whereDate('date', '>=', $dateDebut)
                    ->whereDate('date', '<=', $dateFin);
                break;

            case '2': // Mois
                $mois = $request->input('mois');
                $annee = $request->input('annee');

                if ($request->has('mois_fin') && $request->has('annee_fin')) {
                    // Recherche entre deux mois
                    $moisFin = $request->input('mois_fin');
                    $anneeFin = $request->input('annee_fin');

                    // Calculer la date de début et de fin
                    $dateDebut = "$annee-" . str_pad($mois, 2, '0', STR_PAD_LEFT) . "-01";

                    // Dernier jour du mois de fin
                    $dernierJour = date('t', strtotime("$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-01"));
                    $dateFin = "$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-$dernierJour";

                    $query->whereDate('date', '>=', $dateDebut)
                        ->whereDate('date', '<=', $dateFin);
                } else {
                    // Recherche pour un seul mois
                    $query->whereYear('date', $annee)
                        ->whereMonth('date', $mois);
                }
                break;

            case '3': // Ans
                $annee = $request->input('annee');

                if ($request->has('annee_fin')) {
                    // Recherche entre deux années
                    $anneeFin = $request->input('annee_fin');

                    $query->whereYear('date', '>=', $annee)
                        ->whereYear('date', '<=', $anneeFin);
                } else {
                    // Recherche pour une seule année
                    $query->whereYear('date', $annee);
                }
                break;
        }

        // Obtenir les résultats groupés par type de constat
        $resultats = $query->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->select('constats.code', DB::raw('count(*) as nombre'))
            ->groupBy('constats.code')
            ->get();

        // Calculer le total pour les pourcentages
        $total = $resultats->sum('nombre');

        // Calculer les pourcentages pour chaque type de constat
        $statistiques = $resultats->map(function($item) use ($total) {
            $pourcentage = $total > 0 ? ($item->nombre / $total) * 100 : 0;

            return [
                'code' => $item->code,
                'nombre' => (int)$item->nombre,
                'pourcentage' => $pourcentage
            ];
        });

        return response()->json($statistiques);
    }

    public function indexAI(Request $request)
    {
        $query = Actions::query();

        // Ajouter le filtre pour num_actions commençant par AI-
        $query->where('num_actions', 'like', 'AI-%');

        // Filtrer par utilisateur si spécifié
        if ($request->has('user_id') && !empty($request->user_id)) {
            $query->where('users_id', $request->user_id);
        }

        // Appliquer les filtres de date selon le type de recherche
        $type = $request->input('type', '1');

        switch ($type) {
            case '1': // Jours
                $dateDebut = $request->input('date_debut');
                $dateFin = $request->input('date_fin', $dateDebut);

                $query->whereDate('date', '>=', $dateDebut)
                    ->whereDate('date', '<=', $dateFin);
                break;

            case '2': // Mois
                $mois = $request->input('mois');
                $annee = $request->input('annee');

                if ($request->has('mois_fin') && $request->has('annee_fin')) {
                    // Recherche entre deux mois
                    $moisFin = $request->input('mois_fin');
                    $anneeFin = $request->input('annee_fin');

                    // Calculer la date de début et de fin
                    $dateDebut = "$annee-" . str_pad($mois, 2, '0', STR_PAD_LEFT) . "-01";

                    // Dernier jour du mois de fin
                    $dernierJour = date('t', strtotime("$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-01"));
                    $dateFin = "$anneeFin-" . str_pad($moisFin, 2, '0', STR_PAD_LEFT) . "-$dernierJour";

                    $query->whereDate('date', '>=', $dateDebut)
                        ->whereDate('date', '<=', $dateFin);
                } else {
                    // Recherche pour un seul mois
                    $query->whereYear('date', $annee)
                        ->whereMonth('date', $mois);
                }
                break;

            case '3': // Ans
                $annee = $request->input('annee');

                if ($request->has('annee_fin')) {
                    // Recherche entre deux années
                    $anneeFin = $request->input('annee_fin');

                    $query->whereYear('date', '>=', $annee)
                        ->whereYear('date', '<=', $anneeFin);
                } else {
                    // Recherche pour une seule année
                    $query->whereYear('date', $annee);
                }
                break;
        }

        // Obtenir les résultats groupés par type de constat
        $resultats = $query->join('constats', 'actions.constats_id', '=', 'constats.id')
            ->select('constats.code', DB::raw('count(*) as nombre'))
            ->groupBy('constats.code')
            ->get();

        // Calculer le total pour les pourcentages
        $total = $resultats->sum('nombre');

        // Calculer les pourcentages pour chaque type de constat
        $statistiques = $resultats->map(function($item) use ($total) {
            $pourcentage = $total > 0 ? ($item->nombre / $total) * 100 : 0;

            return [
                'code' => $item->code,
                'nombre' => (int)$item->nombre,
                'pourcentage' => $pourcentage
            ];
        });

        //Log::info('Statistiques AI:', $statistiques->toArray());

        return response()->json($statistiques);
    }

    /**
     * Récupérer la liste des utilisateurs
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers()
    {
        $users = User::select('id', 'nom_utilisateur')->get();
        return response()->json($users);
    }
}
