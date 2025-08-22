<?php

namespace App\Services;

use App\Models\ScheduledNotification;
use Carbon\Carbon;

class FrequenceNotificationService
{
    /**
     * Crée toutes les notifications pour une fréquence donnée
     */
    public function scheduleNotifications($entity, array $frequenceData)
    {
        $this->clearExistingNotifications($entity);

        switch ($frequenceData['type']) {
            case 'Ponctuel':
                $this->schedulePonctuelNotifications($entity, $frequenceData);
                break;
            case 'Hebdomadaire':
                $this->scheduleHebdomadaireNotifications($entity, $frequenceData);
                break;
            case 'Annuel':
                $this->scheduleAnnuelNotifications($entity, $frequenceData);
                break;
            case 'Mensuel':
            case 'Bimestriel':
            case 'Trimestriel':
            case 'Quadrimestriel':
            case 'Semestriel':
                $this->schedulePeriodiques($entity, $frequenceData);
                break;
        }
    }

    /**
     * Programme les notifications pour un événement ponctuel
     */
    private function schedulePonctuelNotifications($entity, array $frequenceData)
    {
        if (!isset($frequenceData['creneaux']))
            return;

        foreach ($frequenceData['creneaux'] as $creneau) {
            // Notifications de commencement (7 jours avant le début)
            if (isset($creneau['debut'])) {
                $debut = Carbon::parse($creneau['debut']);
                $this->createProgressiveNotifications($entity, $debut, 'commencement', 'ponctuel', 7);

                // Notification jour J
                $this->createNotification($entity, $debut, $debut, 'jour_j', 'ponctuel');
            }

            // Notifications de retard (7 jours après la fin)
            if (isset($creneau['fin'])) {
                $fin = Carbon::parse($creneau['fin']);
                $this->createProgressiveNotifications($entity, $fin, 'retard', 'ponctuel', 7, true);
            }

            // Notifications pour les suivis (4 jours avant et après)
            if (isset($creneau['suivis'])) {
                foreach ($creneau['suivis'] as $suivi) {
                    $dateSuivi = Carbon::parse($suivi);
                    $this->createProgressiveNotifications($entity, $dateSuivi, 'suivi_commencement', 'ponctuel', 4);
                    $this->createNotification($entity, $dateSuivi, $dateSuivi, 'suivi_jour_j', 'ponctuel');
                    $this->createProgressiveNotifications($entity, $dateSuivi, 'suivi_retard', 'ponctuel', 4, true);
                }
            }
        }
    }

    /**
     * Programme les notifications pour un événement hebdomadaire
     */
    private function scheduleHebdomadaireNotifications($entity, array $frequenceData)
    {
        if (!isset($frequenceData['dateHeure']['blocs']))
            return;

        foreach ($frequenceData['dateHeure']['blocs'] as $bloc) {
            if (isset($bloc['debut'])) {
                $debut = Carbon::parse($bloc['debut']);
                $this->createProgressiveNotifications($entity, $debut, 'commencement', 'hebdomadaire', 7);
                $this->createNotification($entity, $debut, $debut, 'jour_j', 'hebdomadaire');
            }

            if (isset($bloc['fin'])) {
                $fin = Carbon::parse($bloc['fin']);
                $this->createProgressiveNotifications($entity, $fin, 'retard', 'hebdomadaire', 7, true);
            }
        }

        // Suivis globaux
        if (isset($frequenceData['dateHeure']['suivis'])) {
            foreach ($frequenceData['dateHeure']['suivis'] as $suivi) {
                $dateSuivi = Carbon::parse($suivi);
                $this->createProgressiveNotifications($entity, $dateSuivi, 'suivi_commencement', 'hebdomadaire', 4);
                $this->createNotification($entity, $dateSuivi, $dateSuivi, 'suivi_jour_j', 'hebdomadaire');
                $this->createProgressiveNotifications($entity, $dateSuivi, 'suivi_retard', 'hebdomadaire', 4, true);
            }
        }
    }

    /**
     * Programme les notifications pour un événement annuel
     */
    private function scheduleAnnuelNotifications($entity, array $frequenceData)
    {
        if (!isset($frequenceData['dateHeure']))
            return;

        $debut = Carbon::parse($frequenceData['dateHeure']['debut']);

        // Créer des notifications pour les prochaines années (par exemple 5 ans)
        for ($annee = 0; $annee < 5; $annee++) {
            $debutAnnuel = $debut->copy()->addYears($annee);

            // Seulement si c'est dans le futur
            if ($debutAnnuel->isFuture()) {
                $this->createProgressiveNotifications($entity, $debutAnnuel, 'commencement', 'annuel', 7);
                $this->createNotification($entity, $debutAnnuel, $debutAnnuel, 'jour_j', 'annuel');
            }
        }

        // Notifications de fin si définie
        if (isset($frequenceData['dateHeure']['fin'])) {
            $fin = Carbon::parse($frequenceData['dateHeure']['fin']);
            $this->createProgressiveNotifications($entity, $fin, 'retard', 'annuel', 7, true);
        }

        // Suivis annuels
        if (isset($frequenceData['dateHeure']['suivis'])) {
            foreach ($frequenceData['dateHeure']['suivis'] as $suivi) {
                $dateSuivi = Carbon::parse($suivi);
                $this->createProgressiveNotifications($entity, $dateSuivi, 'suivi_commencement', 'annuel', 4);
                $this->createNotification($entity, $dateSuivi, $dateSuivi, 'suivi_jour_j', 'annuel');
                $this->createProgressiveNotifications($entity, $dateSuivi, 'suivi_retard', 'annuel', 4, true);
            }
        }
    }

    /**
     * Programme les notifications pour les fréquences périodiques
     */
    private function schedulePeriodiques($entity, array $frequenceData)
    {
        if (!isset($frequenceData['planification']))
            return;

        $planification = $frequenceData['planification'];
        $debut = Carbon::parse($planification['dateHeureDebut']);

        // Déterminer la date de fin
        $fin = null;
        if (isset($planification['dateHeureFinale'])) {
            $fin = Carbon::parse($planification['dateHeureFinale']);
        } elseif (isset($planification['dateHeureFin'])) {
            $fin = Carbon::parse($planification['dateHeureFin']);
        }

        // Déterminer l'intervalle
        $intervalles = [
            'Mensuel' => 1,
            'Bimestriel' => 2,
            'Trimestriel' => 3,
            'Quadrimestriel' => 4,
            'Semestriel' => 6
        ];

        $intervalMois = $intervalles[$frequenceData['type']] ?? 1;

        // Créer des notifications récurrentes
        $dateActuelle = $debut->copy();
        while ($fin === null || $dateActuelle->lte($fin)) {
            if ($dateActuelle->isFuture()) {
                $this->createProgressiveNotifications($entity, $dateActuelle, 'commencement', strtolower($frequenceData['type']), 7);
                $this->createNotification($entity, $dateActuelle, $dateActuelle, 'jour_j', strtolower($frequenceData['type']));
            }

            $dateActuelle->addMonths($intervalMois);

            // Sécurité pour éviter une boucle infinie
            if ($dateActuelle->year > now()->addYears(10)->year)
                break;
        }

        // Notification de retard pour la date de fin
        if ($fin) {
            $this->createProgressiveNotifications($entity, $fin, 'retard', strtolower($frequenceData['type']), 7, true);
        }

        // Suivis
        if (isset($planification['suivis'])) {
            foreach ($planification['suivis'] as $suivi) {
                $dateSuivi = Carbon::parse($suivi['dateHeure']);
                $this->createProgressiveNotifications($entity, $dateSuivi, 'suivi_commencement', strtolower($frequenceData['type']), 4);
                $this->createNotification($entity, $dateSuivi, $dateSuivi, 'suivi_jour_j', strtolower($frequenceData['type']));
                $this->createProgressiveNotifications($entity, $dateSuivi, 'suivi_retard', strtolower($frequenceData['type']), 4, true);
            }
        }
    }

    /**
     * Crée des notifications progressives (J-7, J-6, ..., J-1)
     */
    private function createProgressiveNotifications($entity, Carbon $targetDate, string $type, string $frequenceType, int $days, bool $after = false)
    {
        for ($i = 1; $i <= $days; $i++) {
            $triggerDate = $after ?
                $targetDate->copy()->addDays($i) :
                $targetDate->copy()->subDays($i);

            if ($triggerDate->isFuture()) {
                $this->createNotification($entity, $triggerDate, $targetDate, $type, $frequenceType, [
                    'days_remaining' => $after ? -$i : $i,
                    'is_progressive' => true
                ]);
            }
        }
    }

    /**
     * Crée une notification individuelle
     */
    private function createNotification($entity, Carbon $triggerDate, Carbon $targetDate, string $type, string $frequenceType, array $additionalData = [])
    {
        ScheduledNotification::create([
            'type' => $type,
            'frequence_type' => $frequenceType,
            'entity_id' => $entity->id,
            'entity_type' => get_class($entity),
            'trigger_date' => $triggerDate,
            'target_date' => $targetDate,
            'notification_data' => array_merge([
                'entity_name' => $entity->name ?? 'Entité',
                'target_date_formatted' => $targetDate->format('d/m/Y H:i'),
            ], $additionalData)
        ]);
    }

    /**
     * Supprime les notifications existantes pour une entité
     */
    private function clearExistingNotifications($entity)
    {
        ScheduledNotification::where('entity_id', $entity->id)
            ->where('entity_type', get_class($entity))
            ->where('sent', false)
            ->delete();
    }
}
