<?php

namespace App\Services;

use App\Models\EmailConfig;
use App\Models\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class EmailService
{
    public function toggleNotifications(bool $active, int $userId): array
    {
        try {
            $emailConfig = EmailConfig::where('user_id', $userId)->first();

            if (!$emailConfig) {
                return [
                    'success' => false,
                    'message' => 'Configuration email non trouvée'
                ];
            }

            $emailConfig->update(['is_active' => $active]);

            return [
                'success' => true,
                'message' => 'Notifications ' . ($active ? 'activées' : 'désactivées')
            ];
        } catch (\Exception $e) {
            Log::error('Erreur toggle notifications: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des notifications'
            ];
        }
    }

    public function saveConfig(array $config, int $userId): array
    {
        try {
            EmailConfig::updateOrCreate(
                ['user_id' => $userId],
                [
                    'host' => $config['host'],
                    'port' => $config['port'] ?? 587,
                    'username' => $config['username'],
                    'password' => $config['password'],
                    'is_active' => true
                ]
            );

            return [
                'success' => true,
                'message' => 'Configuration sauvegardée'
            ];
        } catch (\Exception $e) {
            Log::error('Erreur sauvegarde config: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde'
            ];
        }
    }

    public function getConfig(int $userId): ?array
    {
        try {
            $config = EmailConfig::where('user_id', $userId)->first();

            if (!$config) {
                return null;
            }

            return [
                'host' => $config->host,
                'port' => $config->port,
                'username' => $config->username,
                'password' => '', // Ne pas retourner le mot de passe
                'is_active' => $config->is_active
            ];
        } catch (\Exception $e) {
            Log::error('Erreur récupération config: ' . $e->getMessage());
            return null;
        }
    }

    public function sendAlert(array $alertData, int $userId): array
    {
        try {
            // Récupérer la configuration email de l'utilisateur
            $emailConfig = EmailConfig::where('user_id', $userId)->first();

            if (!$emailConfig || !$emailConfig->is_active) {
                return [
                    'success' => false,
                    'message' => 'Configuration email non active'
                ];
            }

            // Récupérer l'email de l'utilisateur destinataire
            $user = User::find($userId);
            if (!$user || !$user->email) {
                return [
                    'success' => false,
                    'message' => 'Email utilisateur non trouvé'
                ];
            }

            return $this->sendEmail($emailConfig, $user->email, $alertData);

        } catch (\Exception $e) {
            Log::error('Erreur envoi alerte: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'alerte'
            ];
        }
    }

    private function sendEmail(EmailConfig $config, string $recipientEmail, array $alertData): array
    {
        $mail = new PHPMailer(true);

        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = $config->host;
            $mail->SMTPAuth = true;
            $mail->Username = $config->username;
            $mail->Password = $config->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config->port;
            $mail->CharSet = 'UTF-8';

            // Expéditeur
            $mail->setFrom($config->username, 'Système d\'alertes');

            // Destinataire
            $mail->addAddress($recipientEmail);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $alertData['sujet'];

            // Corps du message HTML
            $htmlBody = $this->formatHtmlMessage($alertData);
            $mail->Body = $htmlBody;

            // Version texte alternative
            $mail->AltBody = strip_tags($alertData['message']);

            $mail->send();

            Log::info('Email envoyé avec succès à: ' . $recipientEmail);

            return [
                'success' => true,
                'message' => 'Email envoyé avec succès'
            ];

        } catch (Exception $e) {
            Log::error('Erreur PHPMailer: ' . $mail->ErrorInfo);
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi: ' . $mail->ErrorInfo
            ];
        }
    }

    private function formatHtmlMessage(array $alertData): string
{
    // Simplification : utiliser directement les valeurs sans conversion complexe
    $typeLabels = [
        'debut' => 'début',
        'suivi' => 'suivi',
    ];

    $type = $typeLabels[$alertData['type']] ?? $alertData['type'];
    $description = $alertData['item']['description'] ?? 'Action';
    $icon = $type === 'debut' ? '🔔' : ($type === 'suivi' ? '📊' : '⚠️');
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Alerte {$type} d'action</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
        <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
            <h2 style='color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 10px;'>
                {$icon} Alerte {$type} d'action
            </h2>

            <div style='background-color: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p style='margin: 0; font-size: 16px;'>
                    <strong>Action :</strong> {$description}
                </p>
            </div>

            <div style='background-color: #fef3c7; padding: 15px; border-radius: 6px; border-left: 4px solid #f59e0b;'>
                <p style='margin: 0;'>{$alertData['message']}</p>
            </div>

            <hr style='margin: 30px 0; border: none; border-top: 1px solid #e5e7eb;'>

            <p style='font-size: 12px; color: #6b7280; text-align: center; margin: 0;'>
                Cet email a été envoyé automatiquement par votre système d'alertes du Salama_tsaa.
            </p>
        </div>
    </body>
    </html>";
}
}
