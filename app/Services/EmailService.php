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
    public function toggleNotifications(bool $isActive, int $userId): array
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ];
            }

            if ($user->role === 'admin') {
                // Pour l'admin, vérifier que sa configuration existe
                $config = EmailConfig::where('user_id', $userId)->first();
                if (!$config) {
                    return [
                        'success' => false,
                        'message' => 'Configuration non trouvée'
                    ];
                }
            } else {
                // Pour les utilisateurs normaux, créer ou mettre à jour leur préférence
                $config = EmailConfig::firstOrCreate(
                    ['user_id' => $userId],
                    [
                        'host' => '', // Vide car ils utilisent la config admin
                        'port' => 587,
                        'username' => '',
                        'password' => '',
                        'is_active' => false
                    ]
                );
            }

            $config->is_active = $isActive;
            $config->save();

            return [
                'success' => true,
                'message' => 'Notifications ' . ($isActive ? 'activées' : 'désactivées')
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
            // Vérifier que seul l'admin peut sauvegarder une configuration complète
            $user = User::find($userId);
            if (!$user || $user->role !== 'admin') {
                return [
                    'success' => false,
                    'message' => 'Seul l\'administrateur peut configurer les paramètres email'
                ];
            }

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
            $user = User::find($userId);
            if (!$user) {
                return null;
            }

            $config = EmailConfig::where('user_id', $userId)->first();

            if ($user->role === 'admin') {
                // Pour l'admin, retourner sa configuration complète
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
            } else {
                // Pour les utilisateurs normaux, retourner seulement le statut
                return [
                    'host' => '',
                    'port' => 587,
                    'username' => '',
                    'password' => '',
                    'is_active' => $config ? $config->is_active : false
                ];
            }

        } catch (\Exception $e) {
            Log::error('Erreur récupération config: ' . $e->getMessage());
            return null;
        }
    }

    public function sendAlert(array $alertData, int $userId): array
    {
        try {
            // Récupérer l'utilisateur
            $user = User::find($userId);
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ];
            }

            // Récupérer la configuration email
            $config = EmailConfig::where('user_id', $userId)->first();

            // Si l'utilisateur n'a pas de config (cas des users normaux), utiliser celle de l'admin
            if (!$config || empty($config->host)) {
                $adminUser = User::where('role', 'admin')->first();
                if (!$adminUser) {
                    return [
                        'success' => false,
                        'message' => 'Aucune configuration admin trouvée'
                    ];
                }
                $adminConfig = EmailConfig::where('user_id', $adminUser->id)->first();
                if (!$adminConfig) {
                    return [
                        'success' => false,
                        'message' => 'Configuration admin non trouvée'
                    ];
                }
                // Utiliser la config admin pour l'envoi, mais vérifier que l'user a activé ses notifications
                if (!$config || !$config->is_active) {
                    return [
                        'success' => false,
                        'message' => 'Notifications désactivées pour cet utilisateur'
                    ];
                }
                $config = $adminConfig; // Utiliser la config admin pour l'envoi
            }

            if (!$config->is_active) {
                return [
                    'success' => false,
                    'message' => 'Configuration email inactive'
                ];
            }

            return $this->sendEmail($config, $user, $alertData);

        } catch (\Exception $e) {
            Log::error('Erreur envoi alerte: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'alerte'
            ];
        }
    }

    private function sendEmail(EmailConfig $config, User $user, array $alertData): array
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
            $mail->setFrom($config->username, 'Système d\'alertes Salama_tsaa');

            // Destinataire : toujours l'email de l'utilisateur qui reçoit l'alerte
            $mail->addAddress($user->email);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $alertData['sujet'];

            // Corps du message HTML
            $htmlBody = $this->formatHtmlMessage($alertData);
            $mail->Body = $htmlBody;

            // Version texte alternative
            $mail->AltBody = strip_tags($alertData['message']);

            $mail->send();

            Log::info('Email envoyé avec succès à: ' . $user->email);

            return [
                'success' => true,
                'message' => 'Alerte envoyée avec succès'
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
