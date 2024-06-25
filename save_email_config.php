<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        echo json_encode(["status" => "error", "message" => "Email e senha são necessários."]);
        exit();
    }

    $config = [
        'email' => $email,
        'senha' => $senha // Não criptografar a senha
    ];

    file_put_contents('email_config.json', json_encode($config));
    echo json_encode(["status" => "success", "message" => "Configurações de email salvas com sucesso!"]);
}
?>
