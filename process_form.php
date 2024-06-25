<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email) || empty($telefone) || empty($usuario) || empty($senha)) {
        echo json_encode(["status" => "error", "message" => "Todos os campos são necessários."]);
        exit();
    }

    $usersDataFile = 'users_data.json';
    $usersData = file_exists($usersDataFile) ? json_decode(file_get_contents($usersDataFile), true) : [];

    $novoUsuario = [
        'nome' => $nome,
        'email' => $email,
        'telefone' => $telefone,
        'usuario' => $usuario,
        'senha' => password_hash($senha, PASSWORD_DEFAULT)
    ];

    $usersData[] = $novoUsuario;
    file_put_contents($usersDataFile, json_encode($usersData, JSON_PRETTY_PRINT));

    // Enviar email para o administrador
    $adminDataFile = 'email_config.json';
    $adminData = file_exists($adminDataFile) ? json_decode(file_get_contents($adminDataFile), true) : null;

    if ($adminData) {
        $adminEmail = $adminData['email'];
        $adminPassword = $adminData['senha'];

        require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
        require 'vendor/phpmailer/phpmailer/src/SMTP.php';
        require 'vendor/phpmailer/phpmailer/src/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Altere para o seu host SMTP
        $mail->SMTPAuth = true;
        $mail->Username = $adminEmail;
        $mail->Password = 'sua_senha_de_app'; // Use a senha de app
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($adminEmail, 'Admin');
        $mail->addAddress($adminEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Novo usuário cadastrado';
        $mail->Body = "Um novo usuário foi cadastrado:<br><br>Nome: $nome<br>Email: $email<br>Telefone: $telefone<br>Usuário: $usuario";

        if (!$mail->send()) {
            error_log("Falha ao enviar email para o administrador. Erro: " . $mail->ErrorInfo);
            echo json_encode(["status" => "success", "message" => "Cadastro realizado com sucesso, mas falha ao enviar email ao administrador.", "usuario" => $usuario]);
        } else {
            echo json_encode(["status" => "success", "message" => "Cadastro realizado com sucesso e email enviado ao administrador.", "usuario" => $usuario]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Configurações de email do administrador não encontradas."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método não suportado."]);
}
?>
