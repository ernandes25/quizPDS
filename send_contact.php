<?php
// Incluir o autoloader do Composer
require 'vendor/autoload.php';

// Usar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';

    // Dados do formulário
    $contactData = [
        'nome' => $nome,
        'email' => $email,
        'telefone' => $telefone,
        'mensagem' => $mensagem
    ];

    // Função para enviar email ao administrador usando PHPMailer
    function sendAdminEmail($contactData) {
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Substitua pelo seu servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'contato@ercont.com.br'; // Substitua pelo seu email
            $mail->Password = ''; // Substitua pela sua senha
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinatários
            $mail->setFrom('no-reply@example.com', 'No Reply');
            $mail->addAddress('contato@ercont.com.br', 'Administrador'); // Substitua pelo email do administrador

            // Conteúdo do email
            $mail->isHTML(true);
            $mail->Subject = 'Nova mensagem de contato';
            $mail->Body    = "Nova mensagem de contato:<br><br>" .
                             "Nome: " . htmlspecialchars($contactData['nome']) . "<br>" .
                             "Email: " . htmlspecialchars($contactData['email']) . "<br>" .
                             "Telefone: " . htmlspecialchars($contactData['telefone']) . "<br>" .
                             "Mensagem: " . nl2br(htmlspecialchars($contactData['mensagem']));

            $mail->send();
            return 'Email enviado com sucesso para o administrador.';
        } catch (Exception $e) {
            return "Falha ao enviar email para o administrador. Erro: {$mail->ErrorInfo}";
        }
    }

    // Enviar email ao administrador
    $result = sendAdminEmail($contactData);
    echo $result;
} else {
    echo "Método de requisição inválido.";
}
?>
