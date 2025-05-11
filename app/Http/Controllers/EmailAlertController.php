<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\EmailConfig;
use App\Models\EmailMember;
use App\Services\EmailService;
use Exception;

class EmailAlertController extends Controller
{
    /**
     * Envoie une alerte par email
     */
    public function sendAlert(Request $request, $userId)
    {
        try {
            $validated = $request->validate([
                'sujet' => 'required|string|max:255',
                'message' => 'required|string',
                'type' => 'required|string|in:debut,suivi,fin',
                'item' => 'required|array',
            ]);

            // Vérifier si la configuration email est active
            $config = EmailConfig::where('user_id', $userId)->first();

            if (!$config || !$config->is_active) {
                return response()->json(['error' => 'Configuration email inactive'], 400);
            }

            // Récupérer les destinataires
            $recipients = EmailMember::where('user_id', $userId)
                ->pluck('email')
                ->toArray();

            if (empty($recipients)) {
                return response()->json(['error' => 'Aucun destinataire configuré'], 400);
            }

            // Préparer les données pour l'envoi
            $emailData = [
                'subject' => $validated['sujet'],
                'message' => $validated['message'],
                'config' => [
                    'host' => $config->host,
                    'port' => $config->port,
                    'username' => $config->username,
                    'password' => decrypt($config->password),
                ],
                'recipients' => $recipients,
                'item' => $validated['item'],
                'type' => $validated['type']
            ];

            // Envoyer l'email
            $emailService = new EmailService();
            $result = $emailService->sendEmail($emailData);

            if (!$result) {
                return response()->json(['error' => 'Échec de l\'envoi'], 500);
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi d\'une alerte email: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
        }
    }
}
