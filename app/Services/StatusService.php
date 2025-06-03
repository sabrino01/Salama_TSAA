<?php

namespace App\Services;

use App\Models\Action;
use App\Models\Actions;
use Illuminate\Support\Facades\Log;

class StatusService
{
    public function checkAndUpdateSingleAction($actionId)
    {
        try {
            $action = Actions::find($actionId);
            if (!$action) {
                return ['success' => false, 'message' => 'Action non trouvée'];
            }

            $wasUpdated = $action->updateStatusIfOverdue();

            return [
                'success' => true,
                'updated' => $wasUpdated,
                'status' => $action->statut,
                'message' => $wasUpdated ? 'Statut mis à jour en "En retard"' : 'Aucune mise à jour nécessaire'
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du statut: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la vérification'];
        }
    }

    public function checkAndUpdateAllActions()
    {
        try {
            $actions = Actions::whereIn('statut', ['En cours', 'En retard'])
                           ->whereNotNull('frequence')
                           ->get();

            $updatedCount = 0;
            foreach ($actions as $action) {
                if ($action->updateStatusIfOverdue()) {
                    $updatedCount++;
                }
            }

            return [
                'success' => true,
                'total_checked' => $actions->count(),
                'updated_count' => $updatedCount,
                'message' => "{$updatedCount} actions mises à jour"
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification globale: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la vérification globale'];
        }
    }
}
