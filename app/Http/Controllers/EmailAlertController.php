<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\EmailConfig;
use App\Models\EmailMember;
use App\Services\EmailService;
use Illuminate\Validation\ValidationException;
use Exception;

class EmailAlertController extends Controller
{
    /**
     * Envoie une alerte par email
     */
    public function sendAlert(Request $request, $userId)
{
    try {
        Log::info('Début sendAlert', [
            'userId' => $userId,
            'requestData' => $request->all()
        ]);

        $validated = $request->validate([
            'sujet' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:debut,suivi,suivis,fin',
            'item' => 'required|array',
        ]);

        Log::info('Validation réussie', ['validated' => $validated]);

        // Vérifier si la configuration email est active
        $config = EmailConfig::where('user_id', $userId)->first();
        Log::info('Configuration email trouvée', [
            'configExists' => (bool)$config,
            'isActive' => $config ? $config->is_active : false
        ]);

        if (!$config || !$config->is_active) {
            Log::warning('Configuration email inactive');
            return response()->json(['error' => 'Configuration email inactive'], 400);
        }

        // Récupérer les destinataires
        $recipients = EmailMember::where('user_id', $userId)
            ->pluck('email')
            ->toArray();

        Log::info('Destinataires trouvés', ['count' => count($recipients)]);

        if (empty($recipients)) {
            Log::warning('Aucun destinataire configuré');
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

        Log::info('Données email préparées', [
            'subject' => $emailData['subject'],
            'host' => $emailData['config']['host'],
            'port' => $emailData['config']['port'],
            'recipientsCount' => count($emailData['recipients'])
        ]);

        // Envoyer l'email
        $emailService = new EmailService();
        $result = $emailService->sendEmail($emailData);

        Log::info('Résultat envoi email', ['success' => $result]);

        if (!$result) {
            return response()->json(['error' => 'Échec de l\'envoi'], 500);
        }

        return response()->json(['success' => true]);
    } catch (ValidationException $ve) {
    Log::error('Erreur de validation', [
        'errors' => $ve->errors()
    ]);
    return response()->json(['errors' => $ve->errors()], 422);
} catch (Exception $e) {
    Log::error('Erreur lors de l\'envoi d\'une alerte email', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    return response()->json(['error' => 'Erreur serveur: ' . $e->getMessage()], 500);
}
}
}
