<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminEmail = $_POST['admin_email'] ?? '';
    $adminPassword = $_POST['admin_password'] ?? '';

    // Dados do email do administrador
    $emailConfig = [
        'email' => $adminEmail,
        'password' => $adminPassword
    ];

    // Salvar a configuração em um arquivo JSON
    file_put_contents('email_config.json', json_encode($emailConfig, JSON_PRETTY_PRINT));
    echo "Configurações de email salvas com sucesso!";
} else {
    echo "Método de requisição inválido.";
}
?>
