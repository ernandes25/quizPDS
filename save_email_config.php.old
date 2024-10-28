<?php
// Incluir o autoloader do Composer
require 'vendor/autoload.php';

// Usar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Verificar se a solicitação é POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Validar dados do formulário
    if (empty($email) || empty($senha)) {
        echo json_encode(['status' => 'error', 'message' => 'Email e senha são necessários.']);
        exit;
    }

    // Criptografar a senha
    $senhaCriptografada = password_hash($senha, PASSWORD_DEFAULT);

    // Salvar as configurações de email e senha criptografada em um arquivo JSON
    $emailConfig = [
        'email' => $email,
        'senha' => $senhaCriptografada
    ];

    if (file_put_contents('email_config.json', json_encode($emailConfig))) {
        echo json_encode(['status' => 'success', 'message' => 'Configurações de email salvas com sucesso!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Falha ao salvar as configurações de email.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido.']);
}
?>