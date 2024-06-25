<?php
header('Content-Type: application/json');

// Verifique se os dados foram enviados corretamente
if (!isset($_POST['email']) || !isset($_POST['senha'])) {
    echo json_encode(["status" => "error", "message" => "Email e senha são necessários."]);
    exit;
}

$email = $_POST['email'];
$senha = $_POST['senha'];

$emailConfigFile = 'email_config.json';

if (!file_exists($emailConfigFile)) {
    echo json_encode(["status" => "error", "message" => "Configuração de email não encontrada."]);
    exit;
}

$emailConfig = json_decode(file_get_contents($emailConfigFile), true);

if ($email === $emailConfig['email'] && password_verify($senha, $emailConfig['senha'])) {
    session_start();
    $_SESSION['admin_logged_in'] = true;
    echo json_encode(["status" => "success", "message" => "Login bem-sucedido."]);
} else {
    echo json_encode(["status" => "error", "message" => "Email ou senha inválidos."]);
}
?>
