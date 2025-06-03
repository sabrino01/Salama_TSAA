<?php

namespace App\Services;

use App\Models\Actions;
use App\Models\EmailConfig;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

            // Envoyer l'email à l'utilisateur principal
            $mainResult = $this->sendEmail($config, $user, $alertData);

            // Récupérer et envoyer aux responsables
            $responsablesResult = $this->sendToResponsables($config, $userId, $alertData);

            // Combiner les résultats
            $totalSuccess = ($mainResult['success'] ? 1 : 0) + $responsablesResult['success_count'];
            $totalErrors = ($mainResult['success'] ? 0 : 1) + $responsablesResult['error_count'];

            $message = "Email principal: " . $mainResult['message'];
            if ($responsablesResult['success_count'] > 0) {
                $message .= " | {$responsablesResult['success_count']} responsable(s) notifié(s)";
            }
            if ($responsablesResult['error_count'] > 0) {
                $message .= " | {$responsablesResult['error_count']} erreur(s) responsables";
            }

            return [
                'success' => $totalSuccess > 0,
                'message' => $message,
                'details' => [
                    'main_user' => $mainResult,
                    'responsables' => $responsablesResult
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Erreur envoi alerte: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'alerte'
            ];
        }
    }

    private function sendToResponsables(EmailConfig $config, int $userId, array $alertData): array
    {
        try {
            // Récupérer TOUTES les actions pour cet utilisateur (pas juste la première)
            $actions = DB::table('actions')->where('users_id', $userId)->get();

            // DEBUG: Log des actions trouvées
            Log::info("DEBUG - Actions trouvées pour userId {$userId}:", [
                'count' => $actions->count(),
                'actions' => $actions->map(function ($action) {
                    return [
                        'id' => $action->id ?? 'N/A',
                        'responsables_id' => $action->responsables_id
                    ];
                })->toArray()
            ]);

            if ($actions->isEmpty()) {
                Log::info("DEBUG - Aucune action trouvée pour userId: {$userId}");
                return [
                    'success_count' => 0,
                    'error_count' => 0,
                    'message' => 'Aucune action trouvée',
                    'debug_info' => [
                        'actions_found' => false,
                        'userId' => $userId
                    ]
                ];
            }

            // Collecter tous les IDs des responsables de toutes les actions
            $allResponsableIds = [];

            foreach ($actions as $action) {
                if (!empty($action->responsables_id)) {
                    // Séparer les IDs des responsables (format: "2,20007,1")
                    $responsableIds = explode(',', $action->responsables_id);
                    $responsableIds = array_map('trim', $responsableIds);
                    $responsableIds = array_filter($responsableIds);

                    // Ajouter à la liste globale
                    $allResponsableIds = array_merge($allResponsableIds, $responsableIds);
                }
            }

            // Supprimer les doublons
            $allResponsableIds = array_unique($allResponsableIds);

            // DEBUG: Log des IDs parsés
            Log::info("DEBUG - Tous les IDs responsables collectés:", [
                'total_actions' => $actions->count(),
                'unique_responsables_ids' => $allResponsableIds,
                'count' => count($allResponsableIds)
            ]);

            if (empty($allResponsableIds)) {
                Log::info("DEBUG - Aucun ID responsable valide trouvé dans toutes les actions");
                return [
                    'success_count' => 0,
                    'error_count' => 0,
                    'message' => 'Aucun responsable valide trouvé dans les actions'
                ];
            }

            // Récupérer les utilisateurs responsables
            $responsables = User::whereIn('responsables_id', $allResponsableIds)->get();

            // DEBUG: Log des responsables trouvés
            Log::info("DEBUG - Responsables trouvés:", [
                'count' => $responsables->count(),
                'responsables' => $responsables->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'responsables_id' => $user->responsables_id
                    ];
                })->toArray()
            ]);

            if ($responsables->isEmpty()) {
                Log::info("DEBUG - Aucun utilisateur responsable trouvé dans la table users");
                return [
                    'success_count' => 0,
                    'error_count' => 0,
                    'message' => 'Aucun utilisateur responsable trouvé',
                    'debug_info' => [
                        'searched_ids' => $allResponsableIds,
                        'total_actions_checked' => $actions->count(),
                        'query' => "SELECT * FROM users WHERE responsables_id IN (" . implode(',', $allResponsableIds) . ")"
                    ]
                ];
            }

            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            $sentEmails = [];

            // Envoyer l'email à chaque responsable (sans doublons)
            foreach ($responsables as $responsable) {
                try {
                    // DEBUG: Log avant envoi
                    Log::info("DEBUG - Tentative d'envoi à:", [
                        'responsable_id' => $responsable->id,
                        'email' => $responsable->email,
                        'name' => $responsable->name
                    ]);

                    $result = $this->sendEmail($config, $responsable, $alertData);
                    if ($result['success']) {
                        $successCount++;
                        $sentEmails[] = $responsable->email;
                        Log::info("DEBUG - Email envoyé avec succès à: " . $responsable->email);
                    } else {
                        $errorCount++;
                        $errors[] = "Erreur pour {$responsable->email}: {$result['message']}";
                        Log::error("DEBUG - Erreur envoi à {$responsable->email}: {$result['message']}");
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    $errors[] = "Exception pour {$responsable->email}: {$e->getMessage()}";
                    Log::error("DEBUG - Exception envoi responsable {$responsable->id}: " . $e->getMessage());
                }
            }

            // DEBUG: Résumé final
            Log::info("DEBUG - Résumé envoi responsables:", [
                'total_actions_processed' => $actions->count(),
                'unique_responsables_contacted' => count($allResponsableIds),
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'sent_emails' => $sentEmails,
                'errors' => $errors
            ]);

            return [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'message' => "Envoi terminé: {$successCount} succès, {$errorCount} erreurs sur " . $actions->count() . " actions",
                'errors' => $errors,
                'debug_info' => [
                    'sent_emails' => $sentEmails,
                    'total_actions_processed' => $actions->count(),
                    'total_responsables_found' => $responsables->count(),
                    'unique_responsables_ids' => $allResponsableIds
                ]
            ];

        } catch (\Exception $e) {
            Log::error('DEBUG - Erreur sendToResponsables: ' . $e->getMessage());
            return [
                'success_count' => 0,
                'error_count' => 1,
                'message' => 'Erreur lors de l\'envoi aux responsables: ' . $e->getMessage()
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
            $mail->addAddress($user->email, $user->name);

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

                <!-- Bouton de redirection -->
                <div style='text-align: center; margin-top: 30px;'>
                    <a href='http://127.0.0.1:8000/login' style='
                        display: inline-block;
                        background-color: #2563eb;
                        color: #fff;
                        padding: 12px 24px;
                        text-decoration: none;
                        border-radius: 6px;
                        font-weight: bold;
                    '>Voir</a>
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
