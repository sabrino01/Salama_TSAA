<?php

namespace App\Services;

use App\Models\Actions;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class EmailService
{
    public function sendEmail($data)
{
    try {
        Log::info('Début sendEmail', [
            'subject' => $data['subject'],
            'recipientsCount' => count($data['recipients']),
            'host' => $data['config']['host'],
            'port' => $data['config']['port']
        ]);

        $mail = new PHPMailer(true);

        // Configuration du serveur
        $mail->SMTPDebug = 2; // Activer le débogage SMTP (2 pour plus de détails)
        $mail->Debugoutput = function($str, $level) {
            Log::info("PHPMailer [$level]: $str");
        };

        $mail->isSMTP();
        $mail->Host = $data['config']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $data['config']['username'];
        $mail->Password = $data['config']['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $data['config']['port'];
        $mail->CharSet = 'UTF-8';

        // Expéditeur
        $mail->setFrom($data['config']['username'], 'Système d\'Alertes');

        // Destinataires
        foreach ($data['recipients'] as $recipient) {
            $mail->addAddress($recipient);
            Log::info('Destinataire ajouté', ['email' => $recipient]);
        }

        // Contenu
        $mail->isHTML(true);
        $mail->Subject = $data['subject'];

        // Corps du message
        $htmlContent = $this->generateEmailTemplate($data);
        $mail->Body = $htmlContent;
        $mail->AltBody = strip_tags($data['message']);

        Log::info('Email prêt à être envoyé');
        $mail->send();
        Log::info('Email envoyé avec succès');
        return true;
    } catch (PHPMailerException $e) {
        Log::error('Erreur PHPMailer détaillée', [
            'errorInfo' => $mail->ErrorInfo,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    } catch (Exception $e) {
        Log::error('Erreur exception standard lors de l\'envoi d\'email', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return false;
    }
}

    /**
     * Génère le template HTML pour l'email
     *
     */
    private function generateEmailTemplate($data)
    {
        $itemId = $data['item']['id'] ?? null;
        $itemName = 'Non spécifié';

        if ($itemId) {
            $action = Actions::find($itemId);

            if ($action && $action->users_id) {
                $utilisateur = User::find($action->users_id); // ou User::find(...)
                $itemName = $utilisateur ? $utilisateur->nom_utilisateur : 'Non spécifié';
            }
        }
        $type = $this->formatAlertType($data['type']);

        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4a86e8; color: white; padding: 15px; text-align: center; }
                .content { padding: 20px; background-color: #f9f9f9; }
                .item-info { background-color: #eee; padding: 10px; margin: 10px 0; }
                .footer { text-align: center; font-size: 12px; color: #777; padding: 10px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Alerte - {$type}</h2>
                </div>
                <div class="content">
                    <p>{$data['message']}</p>

                    <div class="item-info">
                        <p><strong>Élément concerné:</strong> {$itemName}</p>
                        <p><strong>Identifiant:</strong> {$itemId}</p>
                    </div>

                    <p>Cette alerte a été générée automatiquement par l'application Salama_TSAA.</p>
                </div>
                <div class="footer">
                    <p>© Système d'Alertes - Ne pas répondre à cet email</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    /**
     * Formate le type d'alerte pour affichage
     *
     * @param string $type
     * @return string
     */
    private function formatAlertType($type)
    {
        switch ($type) {
            case 'debut':
                return 'Début d\'action';
            case 'suivis':
                return 'Suivi d\'action';
            case 'fin':
                return 'Fin d\'action';
            default:
                return 'Information';
        }
    }

    /**
     * Teste la configuration email
     *
     * @param array $config
     * @return boolean
     */
    public function testConfig($config)
    {
        try {
            $mail = new PHPMailer(true);

            // Configuration du serveur
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['port'];

            // Test de connexion uniquement
            $mail->SMTPDebug = SMTP::DEBUG_CONNECTION;
            ob_start();
            $connected = $mail->SmtpConnect();
            ob_end_clean();

            return $connected;
        } catch (PHPMailerException $e) {
            Log::error('Erreur test PHPMailer: ' . $e->getMessage());
            return false;
        } catch (Exception $e) {
            Log::error('Erreur test config: ' . $e->getMessage());
            return false;
        }
    }
}
