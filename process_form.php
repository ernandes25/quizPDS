<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'db_config.php';

session_start();

header('Content-Type: application/json');

// Defina um manipulador de erros para capturar todos os erros e emitir JSON
set_error_handler(function($severity, $message, $file, $line) {
    http_response_code(500);
    error_log("Error: [$severity] $message in $file on line $line", 3, "/opt/lampp/htdocs/quizPDS/error.log");
    echo json_encode([
        'status' => 'error',
        'message' => $message,
        'file' => $file,
        'line' => $line
    ]);
    exit;
});

set_exception_handler(function($exception) {
    http_response_code(500);
    error_log("Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine(), 3, "/opt/lampp/htdocs/quizPDS/error.log");
    echo json_encode([
        'status' => 'error',
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine()
    ]);
    exit;
});

$response = [
    'status' => 'error',
    'message' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.'
];

try {
    error_log("Request received: " . json_encode($_POST), 3, "/opt/lampp/htdocs/quizPDS/error.log");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        // Determine se estamos lidando com JSON ou dados de formulário
        if ($input) {
            error_log('JSON decodificado: ' . print_r($input, true)); // Adicionado para depuração
            $action = $input['action'] ?? '';
        } else {
            $action = $_POST['action'] ?? '';
        }

        error_log("Action: $action", 3, "/opt/lampp/htdocs/quizPDS/error.log");

        if ($action == 'login') {
            $usuario = $_POST['usuario'];
            $senha = $_POST['senha'];
            error_log("Login attempt: usuario=$usuario", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Validar usuário e senha
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
            $stmt->execute([$usuario]);
            $user = $stmt->fetch();

            if ($user && password_verify($senha, $user['senha'])) {
                $response['status'] = 'success';
                $response['message'] = 'Login realizado com sucesso';
                $response['redirect'] = 'quiz.html';
            } else {
                $response['status'] = 'user_not_found';
                $response['message'] = 'Usuário ou senha inválidos. Redirecionando para a página de cadastro.';
                $response['redirect'] = 'cadastro.html';
            }
            echo json_encode($response);
            exit;

        } elseif ($action == 'admin_login') {
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            error_log("Admin login attempt: email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Validar email e senha do administrador
            $stmt = $pdo->prepare("SELECT * FROM admin_emails WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();

            if ($admin && $senha === $admin['senha']) {
                $response['status'] = 'success';
                $response['message'] = 'Login do administrador realizado com sucesso';
                $response['redirect'] = 'admin_dashboard.html';
            } else {
                $response['message'] = 'Email ou senha do administrador inválidos';
            }
            echo json_encode($response);
            exit;

        } elseif ($action == 'get_users') {
            // Carregar dados dos usuários
            $stmt = $pdo->query("SELECT * FROM usuarios");
            $usuarios = $stmt->fetchAll();
            $response['status'] = 'success';
            $response['usuarios'] = $usuarios;
            echo json_encode($response);
            exit;

        } elseif ($action == 'cadastro') {
            $novo_usuario = $_POST['novo_usuario'];
            $nova_senha = $_POST['nova_senha'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            error_log("Cadastro attempt: usuario=$novo_usuario", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Verificar se o usuário já existe
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
            $stmt->execute([$novo_usuario]);
            $user = $stmt->fetch();

            if ($user) {
                $response['message'] = 'Usuário já existe';
            } else {
                $stmt = $pdo->prepare("INSERT INTO usuarios (usuario, senha, nome, email, telefone) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $novo_usuario,
                    password_hash($nova_senha, PASSWORD_DEFAULT),
                    $nome,
                    $email,
                    $telefone
                ]);
                $response['status'] = 'success';
                $response['message'] = 'Cadastro realizado com sucesso';

                // Enviar email ao administrador
                $stmt = $pdo->query("SELECT email, senha FROM admin_emails LIMIT 1");
                $admin = $stmt->fetch();

                if ($admin) {
                    $mail = new PHPMailer(true);
                    try {
                        // Configurações do servidor
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = $admin['email'];
                        $mail->Password = $admin['senha'];
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Recipientes
                        $mail->setFrom($admin['email'], 'Sistema de Cadastro');
                        $mail->addAddress($admin['email']);

                        // Conteúdo do e-mail
                        $mail->isHTML(true);
                        $mail->Subject = 'Novo Cadastro de Usuario';
                        $mail->Body = "Um novo usuário foi cadastrado:<br>Usuário: $novo_usuario";

                        $mail->send();
                    } catch (Exception $e) {
                        error_log("Mailer Error: {$mail->ErrorInfo}", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                    }
                }
            }
            echo json_encode($response);
            exit;

        } elseif ($action == 'contato') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $mensagem = $_POST['mensagem'];
            error_log("Contato attempt: nome=$nome, email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Buscar informações do administrador no banco de dados
            $stmt = $pdo->query("SELECT email, senha FROM admin_emails LIMIT 1");
            $admin = $stmt->fetch();

            if ($admin) {
                $mail = new PHPMailer(true);
                try {
                    // Configurações do servidor
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $admin['email'];
                    $mail->Password = $admin['senha'];
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipientes
                    $mail->setFrom($email, $nome);
                    $mail->addAddress($admin['email'], 'Administrador');

                    // Conteúdo do e-mail
                    $mail->isHTML(true);
                    $mail->Subject = 'Novo Contato do Site';
                    $mail->Body = "Nome: $nome<br>Email: $email<br>Mensagem: $mensagem";

                    $mail->send();
                    $response['status'] = 'success';
                    $response['message'] = 'Mensagem enviada com sucesso.';
                } catch (Exception $e) {
                    $response['message'] = "A mensagem não pôde ser enviada. Erro: {$mail->ErrorInfo}";
                    error_log("Mailer Error: {$mail->ErrorInfo}", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                }
            } else {
                $response['message'] = 'Configuração de email do administrador inválida.';
            }
            echo json_encode($response);
            exit;

        } elseif ($action == 'cadastrar_email_admin') {
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            error_log("Cadastro email admin attempt: email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            $stmt = $pdo->prepare("INSERT INTO admin_emails (email, senha) VALUES (?, ?)");
            if ($stmt->execute([$email, $senha])) {
                $response['status'] = 'success';
                $response['message'] = 'Email do administrador cadastrado com sucesso';
            } else {
                $response['message'] = 'Falha ao salvar o email do administrador';
                error_log("Failed to save admin email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            }
            echo json_encode($response);
            exit;

        } elseif ($action == 'save_quiz_result') {
            $userEmail = $input['user'];
            $score = $input['score'];

            $stmt = $pdo->prepare("UPDATE usuarios SET quiz_result = ? WHERE email = ?");
            if ($stmt->execute([$score, $userEmail])) {
                $response['status'] = 'success';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Usuário não encontrado';
            }
            echo json_encode($response);
            exit;
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Exception: " . $e->getMessage(), 3, "/opt/lampp/htdocs/quizPDS/error.log");
}

echo json_encode($response);
?>
