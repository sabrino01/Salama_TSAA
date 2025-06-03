<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StatusService;

class CheckActionStatuses extends Command
{
    protected $signature = 'actions:check-statuses';
    protected $description = 'Vérifier et mettre à jour les statuts des actions selon leurs fréquences';

    public function handle()
    {
        $statusService = new StatusService();
        $result = $statusService->checkAndUpdateAllActions();

        $this->info($result['message']);

        if ($result['success']) {
            $this->info("Actions vérifiées: {$result['total_checked']}");
            $this->info("Actions mises à jour: {$result['updated_count']}");
        }
    }
}
