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
            'debut' => 'Début',
            'suivi' => 'Suivi',
        ];

        $type = $typeLabels[$alertData['type']] ?? ucfirst($alertData['type']);
        $description = htmlspecialchars($alertData['item']['description'] ?? 'Action');
        $message = htmlspecialchars($alertData['message']);
        $icon = $type === 'Début' ? '🚀' : ($type === 'Suivi' ? '📊' : '⚠️');

        // Définir les couleurs selon le type d'alerte
        $colors = [
            'Début' => ['bg' => '#e0f2fe', 'border' => '#0288d1', 'text' => '#01579b'],
            'Suivi' => ['bg' => '#f3e5f5', 'border' => '#8e24aa', 'text' => '#4a148c'],
            'default' => ['bg' => '#fff3e0', 'border' => '#f57c00', 'text' => '#e65100']
        ];

        $alertColors = $colors[$type] ?? $colors['default'];

        return '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Alerte ' . $type . ' d\'action</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #f5f5f5;
                }
                .container {
                    background-color: #ffffff;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px 25px;
                    text-align: center;
                    position: relative;
                }
                .header::after {
                    content: "";
                    position: absolute;
                    bottom: -10px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 0;
                    height: 0;
                    border-left: 15px solid transparent;
                    border-right: 15px solid transparent;
                    border-top: 10px solid #764ba2;
                }
                .header h2 {
                    margin: 0;
                    font-size: 26px;
                    font-weight: 300;
                    letter-spacing: 1px;
                }
                .header .subtitle {
                    margin: 8px 0 0 0;
                    font-size: 14px;
                    opacity: 0.9;
                    text-transform: uppercase;
                    letter-spacing: 2px;
                }
                .content {
                    padding: 40px 25px 30px;
                }
                .greeting {
                    font-size: 16px;
                    margin-bottom: 25px;
                    color: #555;
                }
                .action-details {
                    background: linear-gradient(145deg, #f8f9fa 0%, #e9ecef 100%);
                    padding: 25px;
                    border-radius: 10px;
                    margin: 25px 0;
                    border-left: 5px solid ' . $alertColors['border'] . ';
                    position: relative;
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                }
                .action-details::before {
                    content: "' . $icon . '";
                    position: absolute;
                    top: -10px;
                    right: 15px;
                    background: white;
                    padding: 8px 12px;
                    border-radius: 50%;
                    font-size: 18px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                }
                .action-details h3 {
                    margin: 0 0 15px 0;
                    color: ' . $alertColors['text'] . ';
                    font-size: 18px;
                    font-weight: 600;
                }
                .action-details p {
                    margin: 12px 0;
                    font-size: 15px;
                }
                .alert-message {
                    background: linear-gradient(135deg, ' . $alertColors['bg'] . ' 0%, rgba(255,255,255,0.8) 100%);
                    padding: 20px;
                    border-radius: 8px;
                    border-left: 4px solid ' . $alertColors['border'] . ';
                    margin: 20px 0;
                    font-style: italic;
                    font-size: 15px;
                    color: ' . $alertColors['text'] . ';
                }
                .btn {
                    display: inline-block;
                    padding: 16px 35px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    text-decoration: none;
                    border-radius: 30px;
                    font-weight: 600;
                    text-transform: uppercase;
                    letter-spacing: 1.2px;
                    font-size: 14px;
                    transition: all 0.3s ease;
                    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                }
                .btn:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
                }
                .btn-container {
                    text-align: center;
                    margin: 35px 0;
                }
                .footer {
                    background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 50%, #f8f9fa 100%);
                    padding: 25px;
                    text-align: center;
                    color: #6c757d;
                    font-size: 13px;
                    border-top: 1px solid #dee2e6;
                }
                .divider {
                    height: 3px;
                    background: linear-gradient(90deg, transparent, #667eea, #764ba2, transparent);
                    margin: 30px 0;
                    border-radius: 2px;
                }
                .system-badge {
                    display: inline-block;
                    background: linear-gradient(135deg, #28a745, #20c997);
                    color: white;
                    padding: 4px 12px;
                    border-radius: 20px;
                    font-size: 11px;
                    font-weight: bold;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-top: 10px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>' . $icon . ' Alerte ' . $type . '</h2>
                    <div class="subtitle">Système de suivi Salama_tsaa</div>
                </div>

                <div class="content">
                    <div class="greeting">
                        Bonjour,
                    </div>

                    <p>Une nouvelle alerte pour le <strong>' . $type . '</strong> d\'action a été générée pour votre attention :</p>

                    <div class="action-details">
                        <h3>📋 Détails de l\'action</h3>
                        <p><strong>Description :</strong> ' . $description . '</p>
                    </div>

                    <div class="alert-message">
                        💬 <strong>Message d\'alerte :</strong><br>
                        ' . $message . '
                    </div>

                    <div class="divider"></div>

                    <p>Veuillez vous connecter à votre espace personnel pour consulter les détails complets.</p>

                    <div class="btn-container">
                        <a href="http://127.0.0.1:8000/login" class="btn">
                            🔗 Accéder au système
                        </a>
                    </div>

                    <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
                        Cordialement,<br>
                    </p>
                </div>

                <div class="footer">
                    <p>
                        📧 Cet email a été envoyé automatiquement par le système d\'alertes.
                        <span class="system-badge">Salama_tsaa</span>
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }
}
