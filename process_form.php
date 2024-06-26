<?php
// Incluir o arquivo de configuração
require 'config.php';

// Incluir o autoloader do Composer
require 'vendor/autoload.php';

// Usar PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Carregar as configurações de email do administrador
$emailConfig = json_decode(file_get_contents('email_config.json'), true);
$adminEmail = $emailConfig['email'];
$adminPassword = getenv('ADMIN_EMAIL_PASSWORD');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $usuario = $_POST['usuario'] ?? '';
    $senha = password_hash($_POST['senha'] ?? '', PASSWORD_BCRYPT);

    // Dados do formulário
    $userData = [
        'nome' => $nome,
        'email' => $email,
        'telefone' => $telefone,
        'usuario' => $usuario,
        'senha' => $senha
    ];

    // Salvar os dados do usuário no arquivo JSON
    $usersData = json_decode(file_get_contents('users_data.json'), true);
    $usersData[] = $userData;
    file_put_contents('users_data.json', json_encode($usersData));

    // Função para enviar email ao administrador usando PHPMailer
    function sendAdminEmail($userData, $adminEmail, $adminPassword) {
        $mail = new PHPMailer(true);
        try {
            // Configurações do servidor
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Substitua pelo seu servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = $adminEmail;
            $mail->Password = $adminPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Destinatários
            $mail->setFrom('no-reply@example.com', 'No Reply');
            $mail->addAddress($adminEmail, 'Administrador'); // Substitua pelo email do administrador

            // Conteúdo do email
            $mail->isHTML(true);
            $mail->Subject = 'Novo usuário cadastrado';
            $mail->Body    = "Novo usuário cadastrado:<br><br>" .
                             "Nome: " . htmlspecialchars($userData['nome']) . "<br>" .
                             "Email: " . htmlspecialchars($userData['email']) . "<br>" .
                             "Telefone: " . htmlspecialchars($userData['telefone']) . "<br>" .
                             "Usuário: " . htmlspecialchars($userData['usuario']);

            $mail->send();
            return 'Email enviado com sucesso para o administrador.';
        } catch (Exception $e) {
            return "Falha ao enviar email para o administrador. Erro: {$mail->ErrorInfo}";
        }
    }

    // Enviar email ao administrador
    $result = sendAdminEmail($userData, $adminEmail, $adminPassword);
    echo json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso e email enviado ao administrador.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido.']);
}
