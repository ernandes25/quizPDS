<?php
require 'vendor/autoload.php'; // Inclua o autoload do Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $adminConfig = json_decode(file_get_contents('email_config.json'), true);
    $adminEmail = $adminConfig['email'];
    $adminPassword = $adminConfig['senha']; // Usar a senha não criptografada

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Use o servidor SMTP adequado
    $mail->SMTPAuth = true;
    $mail->Username = $adminEmail;
    $mail->Password = $adminPassword;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom($adminEmail, 'Administrador');
    $mail->addAddress($adminEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Teste de Envio';
    $mail->Body    = 'Este é um teste de envio de e-mail.';

    $mail->send();
    echo 'Email enviado com sucesso.';
} catch (Exception $e) {
    file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - Falha ao enviar email. Erro: {$mail->ErrorInfo}" . PHP_EOL, FILE_APPEND);
    echo "Falha ao enviar email. Erro: {$mail->ErrorInfo}";
}
?>
