<?php
header('Content-Type: application/json');

// Verifique se os dados foram enviados corretamente
if (!isset($_POST['email']) || !isset($_POST['senha'])) {
    echo json_encode(["status" => "error", "message" => "Email e senha são necessários."]);
    exit;
}

$email = $_POST['email'];
$senha = password_hash($_POST['senha'], PASSWORD_BCRYPT);

$emailConfig = [
    'email' => $email,
    'senha' => $senha,
];

$emailConfigFile = 'email_config.json';

if (file_put_contents($emailConfigFile, json_encode($emailConfig))) {
    echo json_encode(["status" => "success", "message" => "Configurações de email salvas com sucesso!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Falha ao salvar as configurações de email."]);
}
?>
