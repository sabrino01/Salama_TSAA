<?php

namespace App\Http\Controllers;

use App\Models\EmailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\EmailService;
use Exception;

class EmailConfigController extends Controller
{
    /**
     * Récupère la configuration email d'un utilisateur
     */
    public function getConfig($userId)
    {
        try {
            $config = EmailConfig::where('user_id', $userId)->first();

            if (!$config) {
                return response()->json([
                    'host' => '',
                    'port' => 587,
                    'username' => '',
                    'is_active' => false
                ]);
            }

            return response()->json([
                'host' => $config->host,
                'port' => $config->port,
                'username' => $config->username,
                'is_active' => $config->is_active
            ]);
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération de la configuration email: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Sauvegarde la configuration email d'un utilisateur
     */
    public function saveConfig(Request $request, $userId)
    {
        try {
            $validated = $request->validate([
                'host' => 'required|string|max:255',
                'port' => 'required|integer',
                'username' => 'required|email|max:255',
                'password' => 'nullable|string',
            ]);

            // Rechercher une configuration existante ou en créer une nouvelle
            $config = EmailConfig::updateOrCreate(
                ['user_id' => $userId],
                [
                    'host' => $validated['host'],
                    'port' => $validated['port'],
                    'username' => $validated['username'],
                    'is_active' => true
                ]
            );

            // Mettre à jour le mot de passe uniquement s'il est fourni
            if (!empty($validated['password'])) {
                $config->password = encrypt($validated['password']);
                $config->save();
            }

            return response()->json(['success' => true, 'message' => 'Configuration sauvegardée']);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'enregistrement de la configuration email: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Active ou désactive les notifications email pour un utilisateur
     */
    public function toggleNotifications(Request $request, $userId)
    {
        try {
            $validated = $request->validate([
                'active' => 'required|boolean',
            ]);

            $config = EmailConfig::where('user_id', $userId)->first();

            if (!$config) {
                return response()->json(['error' => 'Configuration non trouvée'], 404);
            }

            $config->is_active = $validated['active'];
            $config->save();

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Erreur lors du toggle des notifications: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur'], 500);
        }
    }

    /**
     * Teste la configuration email
     */
    public function testConfig(Request $request)
    {
        try {
            $validated = $request->validate([
                'host' => 'required|string|max:255',
                'port' => 'required|integer',
                'username' => 'required|email|max:255',
                'password' => 'required|string',
            ]);

            $emailService = new EmailService();
            $result = $emailService->testConfig($validated);

            return response()->json(['success' => $result]);
        } catch (Exception $e) {
            Log::error('Erreur lors du test de la configuration email: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
