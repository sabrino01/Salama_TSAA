<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;


class EmailConfigController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function toggleNotifications(Request $request): JsonResponse
    {
        $request->validate([
            'active' => 'required|boolean',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $result = $this->emailService->toggleNotifications(
            $request->active,
            $request->user_id
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function saveConfig(Request $request): JsonResponse
    {
        $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'username' => 'required|string|email',
            'password' => 'required|string',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $config = $request->only(['host', 'port', 'username', 'password']);

        $result = $this->emailService->saveConfig($config, $request->user_id);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function getConfig(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $config = $this->emailService->getConfig($request->user_id);

        if ($config) {
            return response()->json([
                'success' => true,
                'config' => $config
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Configuration non trouvée'
        ], 404);
    }

    public function sendAlert(Request $request): JsonResponse
    {
        $request->validate([
             'sujet' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:debut,suivi',
            'item' => 'required|array',
            'item.description' => 'required|string',
            'user_id' => 'required|integer|exists:users,id'
        ]);

        $alertData = $request->only(['sujet', 'message', 'type', 'item']);

        $result = $this->emailService->sendAlert($alertData, $request->user_id);

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
