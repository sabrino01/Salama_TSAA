<?php
// test-smtp.php - Placez ce fichier à la racine de votre projet Laravel
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

// Récupérer les paramètres depuis la ligne de commande ou utiliser des valeurs par défaut
$host = $argv[1] ?? 'smtp.example.com';
$port = $argv[2] ?? 587;
$username = $argv[3] ?? 'user@example.com';
$password = $argv[4] ?? 'password';
$to = $argv[5] ?? 'recipient@example.com';

echo "Test de connexion SMTP avec les paramètres:\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Username: $username\n";
echo "To: $to\n\n";

$mail = new PHPMailer(true);

try {
    // Configuration du débogage
    $mail->SMTPDebug = 2; // Niveau de débogage

    // Configuration du serveur
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $port;
    $mail->CharSet = 'UTF-8';

    // Expéditeur et destinataire
    $mail->setFrom($username, 'Test SMTP');
    $mail->addAddress($to);

    // Contenu
    $mail->isHTML(true);
    $mail->Subject = 'Test de connexion SMTP';
    $mail->Body = '<h1>Test de connexion SMTP</h1><p>Si vous recevez cet email, la configuration SMTP est correcte!</p>';
    $mail->AltBody = 'Test de connexion SMTP - Si vous recevez cet email, la configuration SMTP est correcte!';

    $mail->send();
    echo "Email de test envoyé avec succès!\n";
} catch (PHPMailerException $e) {
    echo "Erreur lors de l'envoi de l'email: {$mail->ErrorInfo}\n";
} catch (Exception $e) {
    echo "Exception: {$e->getMessage()}\n";
}
