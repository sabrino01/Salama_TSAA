<?php

namespace App\Http\Controllers;

use App\Models\Actions;
use Illuminate\Http\Request;
use App\Services\FrequenceNotificationService;

class FrequenceController extends Controller
{
    private $notificationService;

    public function __construct(FrequenceNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function store(Request $request)
    {
        $action = Actions::create($request->all());

        $frequenceData = json_decode($action->frequence, true);

        $this->notificationService->scheduleNotifications($action, $frequenceData);

        return response()->json(['message' => 'Action créée avec notifications planifiées']);
    }

    public function update(Request $request, $id)
    {
        $action = Actions::findOrFail($id);
        $action->update($request->all());

        $frequenceData = json_decode($action->frequence, true);

        $this->notificationService->scheduleNotifications($action, $frequenceData);

        return response()->json(['message' => 'Action mise à jour avec notifications planifiées']);
    }
}
