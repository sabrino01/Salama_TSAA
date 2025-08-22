<?php

namespace App\Jobs;

use App\Models\ScheduledNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class SendScheduledNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $notifications = ScheduledNotification::pending()
            ->ready()
            ->with('entity')
            ->get();

        foreach ($notifications as $scheduledNotification) {
            try {
                // Send the notification via PHPMailer
                $this->sendEmailNotification($scheduledNotification);

                // Mark as sent
                $scheduledNotification->update([
                    'sent' => true,
                    'sent_at' => now()
                ]);

                Log::info("Notification sent", [
                    'id' => $scheduledNotification->id,
                    'type' => $scheduledNotification->type,
                    'entity_id' => $scheduledNotification->entity_id
                ]);

            } catch (\Exception $e) {
                Log::error("Error sending notification", [
                    'id' => $scheduledNotification->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    private function sendEmailNotification(ScheduledNotification $scheduledNotification)
    {
        $entity = $scheduledNotification->entity;
        if (!$entity) {
            throw new Exception("Entity not found for notification ID {$scheduledNotification->id}");
        }

        // Retrieve email configuration (similar to Vue.js emailService)
        $emailConfig = $this->getEmailConfig($entity->user_id); // Assume user_id is available on entity

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $emailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $emailConfig['username'];
            $mail->Password = $emailConfig['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $emailConfig['port'];

            // Recipients
            $mail->setFrom($emailConfig['username'], 'Notification System');
            $mail->addAddress($entity->email ?? $emailConfig['username']); // Use entity email or fallback

            // Content
            $daysRemaining = $scheduledNotification->notification_data['days_remaining'] ?? 0;
            $message = $this->formatNotificationMessage($scheduledNotification);
            $mail->isHTML(true);
            $mail->Subject = "Alerte: {$scheduledNotification->type} pour {$scheduledNotification->notification_data['entity_name']}";
            $mail->Body = nl2br($message);
            $mail->AltBody = $message;

            $mail->send();
        } catch (Exception $e) {
            throw new Exception("Failed to send email: {$mail->ErrorInfo}");
        }
    }

    private function getEmailConfig($userId)
    {
        // Fetch email configuration from the database or API
        // This should match the logic in your Vue.js emailService.getConfig
        $config = \App\Models\EmailConfig::where('user_id', $userId)->first();
        if (!$config) {
            throw new Exception("Email configuration not found for user ID {$userId}");
        }
        return [
            'host' => $config->host,
            'port' => $config->port,
            'username' => $config->username,
            'password' => $config->password
        ];
    }

    private function formatNotificationMessage(ScheduledNotification $scheduledNotification)
    {
        $data = $scheduledNotification->notification_data;
        $daysRemaining = $data['days_remaining'] ?? 0;
        $entityName = $data['entity_name'] ?? 'Entité';
        $targetDate = $data['target_date_formatted'] ?? $scheduledNotification->target_date->format('d/m/Y H:i');

        if ($scheduledNotification->type === 'jour_j' || $scheduledNotification->type === 'suivi_jour_j') {
            return "AUJOURD'HUI: {$entityName} ({$scheduledNotification->type}) à {$targetDate}";
        } elseif ($daysRemaining > 0) {
            return "Dans {$daysRemaining} jour(s): {$entityName} ({$scheduledNotification->type}) prévu pour {$targetDate}";
        } else {
            $daysLate = abs($daysRemaining);
            return "Retard de {$daysLate} jour(s): {$entityName} ({$scheduledNotification->type}) était prévu pour {$targetDate}";
        }
    }
}
