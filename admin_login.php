<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Adicionar log para verificar se o email e a senha foram recebidos corretamente
    file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - Email: $email, Senha: $senha" . PHP_EOL, FILE_APPEND);

    if (empty($email) || empty($senha)) {
        echo json_encode(["status" => "error", "message" => "Email e senha são necessários."]);
        exit();
    }

    // Ler configurações de email do administrador
    $configFile = 'email_config.json';
    if (!file_exists($configFile)) {
        echo json_encode(["status" => "error", "message" => "Configurações de email não encontradas."]);
        exit();
    }

    $config = json_decode(file_get_contents($configFile), true);
    $adminEmail = $config['email'];
    $adminPassword = $config['senha']; // Senha não criptografada

    // Adicionar log para verificar os valores do email e senha do administrador
    file_put_contents('error_log.txt', date('Y-m-d H:i:s') . " - Admin Email: $adminEmail, Admin Senha: $adminPassword" . PHP_EOL, FILE_APPEND);

    // Verificar se o email e a senha estão corretos
    if ($email === $adminEmail && $senha === $adminPassword) {
        $_SESSION['admin_logged_in'] = true;
        echo json_encode(["status" => "success", "message" => "Login bem-sucedido."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Email ou senha incorretos."]);
    }
}
?>
