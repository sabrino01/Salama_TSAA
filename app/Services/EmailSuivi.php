<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\EmailConfig;
use Illuminate\Support\Facades\DB;

class EmailSuivi
{
    private $phpMailer;

    public function __construct()
    {
        $this->phpMailer = new PHPMailer(true);
    }

    /**
     * Récupère la configuration email pour un utilisateur
     */
    private function getEmailConfig(int $userId): ?array
    {
        try {
            $user = User::find($userId);
            if (!$user) {
                return null;
            }

            $config = EmailConfig::where('user_id', $userId)->first();

            // Si l'utilisateur est admin, on utilise sa configuration
            if ($user->role === 'admin') {
                if (!$config || !$config->is_active) {
                    return null;
                }

                return [
                    'host' => $config->host,
                    'port' => $config->port,
                    'username' => $config->username,
                    'password' => $config->password,
                    'is_active' => $config->is_active
                ];
            } else {
                // Pour les utilisateurs normaux, on cherche la config d'un admin actif
                $adminConfig = EmailConfig::join('users', 'email_configs.user_id', '=', 'users.id')
                    ->where('users.role', 'admin')
                    ->where('email_configs.is_active', true)
                    ->select('email_configs.*')
                    ->first();

                if (!$adminConfig) {
                    return null;
                }

                return [
                    'host' => $adminConfig->host,
                    'port' => $adminConfig->port,
                    'username' => $adminConfig->username,
                    'password' => $adminConfig->password,
                    'is_active' => true
                ];
            }

        } catch (\Exception $e) {
            Log::error('Erreur récupération config email: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Configure PHPMailer avec la configuration de l'utilisateur
     */
    private function configurerPHPMailer($config, $expediteurEmail, $expediteurNom)
    {
        try {
            if (!$config || !$config['is_active']) {
                throw new Exception('Configuration email non disponible ou inactive');
            }

            // Configuration SMTP
            $this->phpMailer->isSMTP();
            $this->phpMailer->Host = $config['host'];
            $this->phpMailer->SMTPAuth = true;
            $this->phpMailer->Username = $config['username'];
            $this->phpMailer->Password = $config['password'];
            $this->phpMailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->phpMailer->Port = $config['port'];

            // Configuration générale
            $this->phpMailer->CharSet = 'UTF-8';
            $this->phpMailer->isHTML(true);

            // Configuration de l'expéditeur
            $this->phpMailer->setFrom($config['username'], $expediteurNom);
            $this->phpMailer->addReplyTo($expediteurEmail, $expediteurNom);

            return true;

        } catch (Exception $e) {
            Log::error('Erreur configuration PHPMailer: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie un email de notification de mise à jour d'action par un suivi
     *
     * @param object $actionInfo - Informations sur l'action et les utilisateurs
     * @param string $nouveauStatut - Le nouveau statut
     * @param string|null $observation - L'observation du suivi
     * @param int $suiviUserId - ID de l'utilisateur suivi (pour la config email)
     * @return bool - true si l'email a été envoyé avec succès
     */
    public function envoyerNotificationMiseAJourSuivi($actionInfo, $nouveauStatut, $observation = null, $suiviUserId = null)
    {
        try {
            // Si pas d'ID utilisateur fourni, essayer de le récupérer depuis les données de l'action
            if (!$suiviUserId) {
                // Extraire l'ID du suivi depuis l'email ou d'autres moyens
                $suiviUserId = $this->getSuiviUserIdFromEmail($actionInfo->suivi_email);
            }

            if (!$suiviUserId) {
                Log::warning('ID utilisateur suivi non trouvé pour l\'envoi d\'email');
                return false;
            }

            // Récupérer la configuration email
            $config = $this->getEmailConfig($suiviUserId);
            if (!$config) {
                Log::warning('Configuration email non disponible pour l\'utilisateur: ' . $suiviUserId);
                return false;
            }

            // Réinitialiser PHPMailer
            $this->phpMailer->clearAddresses();
            $this->phpMailer->clearReplyTos();
            $this->phpMailer->clearAllRecipients();

            // Configurer PHPMailer
            if (!$this->configurerPHPMailer($config, $actionInfo->suivi_email, $actionInfo->suivi_libelle)) {
                return false;
            }

            // Configuration du destinataire et du contenu
            $this->phpMailer->addAddress($actionInfo->user_email);
            $this->phpMailer->Subject = 'Mise à jour d\'action par suivi - ' . $actionInfo->num_actions;
            $this->phpMailer->Body = $this->genererTemplateEmail($actionInfo, $nouveauStatut, $observation);
            $this->phpMailer->AltBody = $this->genererTexteSimple($actionInfo, $nouveauStatut, $observation);

            // Envoyer l'email
            $this->phpMailer->send();

            Log::info('Email de suivi envoyé avec succès', [
                'action_id' => $actionInfo->action_id,
                'destinataire' => $actionInfo->user_email,
                'expediteur' => $actionInfo->suivi_email,
                'suivi_libelle' => $actionInfo->suivi_libelle,
                'config_utilisee' => $config['username']
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Erreur envoi email PHPMailer (suivi): ' . $e->getMessage(), [
                'action_id' => $actionInfo->action_id ?? 'unknown',
                'suivi_email' => $actionInfo->suivi_email ?? 'unknown',
                'user_email' => $actionInfo->user_email ?? 'unknown',
                'suivi_user_id' => $suiviUserId ?? 'unknown',
                'error_details' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Récupère l'ID utilisateur du suivi depuis l'email
     */
    private function getSuiviUserIdFromEmail($suiviEmail)
    {
        try {
            // Option 1: Vérifier si un utilisateur a le même email que le suivi
            $userByEmail = User::where('email', $suiviEmail)->first();
            if ($userByEmail) {
                return $userByEmail->id;
            }

            // Option 2: Si un utilisateur est lié au suivi via suivis_id
            $suivi = DB::table('suivis')->where('email', $suiviEmail)->select('id')->first();

            if ($suivi) {
                $user = DB::table('users')
                    ->where('suivis_id', $suivi->id)
                    ->select('id')
                    ->first();

                if ($user) {
                    return $user->id;
                }
            }

            // Option 3: Retourner null si aucune correspondance trouvée
            return null;

        } catch (\Exception $e) {
            Log::error('Erreur récupération user ID suivi depuis email: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Génère le template HTML de l'email
     */
    private function genererTemplateEmail($actionInfo, $nouveauStatut, $observation)
    {
        $observationHtml = $observation ?
            "<p><strong>Observation :</strong> " . htmlspecialchars($observation) . "</p>" : "";

        return '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Mise à jour d\'action par suivi</title>
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
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                }
                .header {
                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                    color: white;
                    padding: 30px 20px;
                    text-align: center;
                }
                .header h2 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 300;
                }
                .content {
                    padding: 30px 20px;
                }
                .action-details {
                    background-color: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin: 20px 0;
                    border-left: 4px solid #28a745;
                }
                .action-details p {
                    margin: 8px 0;
                }
                .status-badge {
                    display: inline-block;
                    padding: 6px 12px;
                    border-radius: 20px;
                    font-size: 12px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .status-en-cours { background-color: #fff3cd; color: #856404; }
                .status-en-retard { background-color: #f8d7da; color: #721c24; }
                .status-cloture { background-color: #d4edda; color: #155724; }
                .status-abandonne { background-color: #f1f3f4; color: #5f6368; }
                .btn {
                    display: inline-block;
                    padding: 15px 30px;
                    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                    color: white;
                    text-decoration: none;
                    border-radius: 25px;
                    font-weight: bold;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    transition: transform 0.2s;
                }
                .btn:hover {
                    transform: translateY(-2px);
                }
                .btn-container {
                    text-align: center;
                    margin: 30px 0;
                }
                .footer {
                    background-color: #f8f9fa;
                    padding: 20px;
                    text-align: center;
                    color: #6c757d;
                    font-size: 12px;
                    border-top: 1px solid #dee2e6;
                }
                .divider {
                    height: 2px;
                    background: linear-gradient(90deg, transparent, #28a745, transparent);
                    margin: 20px 0;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>🔄 Mise à jour par suivi</h2>
                </div>

                <div class="content">
                    <p>Bonjour,</p>

                    <p>Le responsable du suivi <strong> ' . htmlspecialchars($actionInfo->suivi_libelle) . '</strong> a mis à jour l\'action suivante :</p>

                    <div class="action-details">
                        <p><strong>📄 Numéro d\'action :</strong> ' . htmlspecialchars($actionInfo->num_actions) . '</p>
                        <p><strong>📝 Action :</strong> ' . htmlspecialchars($actionInfo->action_libelle) . '</p>
                        <p><strong>🔄 Nouveau statut :</strong>
                            <span class="status-badge status-' . strtolower(str_replace([' ', 'é'], ['-', 'e'], $nouveauStatut)) . '">
                                ' . htmlspecialchars($nouveauStatut) . '
                            </span>
                        </p>
                        ' . $observationHtml . '
                    </div>

                    <div class="divider"></div>

                    <p>Vous pouvez consulter les détails complets de cette action en vous connectant à votre espace personnel.</p>

                    <div class="btn-container">
                        <a href="http://127.0.0.1:8000/login" class="btn">🔗 Se connecter</a>
                    </div>

                    <p>Cordialement,<br>
                </div>

                <div class="footer">
                    <p>📧 Cet email a été envoyé automatiquement suite à une mise à jour d\'action par un suivi.</p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Génère une version texte simple pour les clients email qui ne supportent pas HTML
     */
    private function genererTexteSimple($actionInfo, $nouveauStatut, $observation)
    {
        $texte = "MISE À JOUR D'ACTION PAR SUIVI\n\n";
        $texte .= "Bonjour,\n\n";
        $texte .= "Le suivi " . $actionInfo->suivi_libelle . " a mis à jour l'action suivante :\n\n";
        $texte .= "Numéro d'action : " . $actionInfo->num_actions . "\n";
        $texte .= "Libellé : " . $actionInfo->action_libelle . "\n";
        $texte .= "Nouveau statut : " . $nouveauStatut . "\n";

        if ($observation) {
            $texte .= "Observation : " . $observation . "\n";
        }

        $texte .= "Mis à jour par : " . $actionInfo->suivi_libelle . "\n";
        $texte .= "\nVous pouvez consulter les détails de cette action en vous connectant :\n";
        $texte .= "http://127.0.0.1:8000/login\n\n";
        $texte .= "Cordialement,\n";
        $texte .= "L'équipe de suivi des actions\n\n";
        $texte .= "---\n";
        $texte .= "Cet email a été envoyé automatiquement par votre application Salama_tsaa.\n";

        return $texte;
    }
}
