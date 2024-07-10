<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

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

        $json_file = '/opt/lampp/htdocs/quizPDS/usuarios.json';
        $admin_email_file = '/opt/lampp/htdocs/quizPDS/admin_email.json';

        if ($action == 'login') {
            $usuario = $_POST['usuario'];
            $senha = $_POST['senha'];
            error_log("Login attempt: usuario=$usuario", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Validar usuário e senha
            if (file_exists($json_file)) {
                $json_data = file_get_contents($json_file);
                $usuarios = json_decode($json_data, true);
                if (!is_array($usuarios)) {
                    $usuarios = [];
                }
                error_log("Loaded users: " . json_encode($usuarios), 3, "/opt/lampp/htdocs/quizPDS/error.log");

                foreach ($usuarios as $user) {
                    if ($user['usuario'] == $usuario && password_verify($senha, $user['senha'])) {
                        $response['status'] = 'success';
                        $response['message'] = 'Login realizado com sucesso';
                        $response['redirect'] = 'quiz.html';
                        echo json_encode($response);
                        exit;
                    }
                }
                $response['status'] = 'user_not_found';
                $response['message'] = 'Usuário ou senha inválidos. Redirecionando para a página de cadastro.';
                $response['redirect'] = 'cadastro.html';
            } else {
                $response['message'] = 'Arquivo de usuários não encontrado';
                error_log("User file not found", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            }
        } elseif ($action == 'admin_login') {
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            error_log("Admin login attempt: email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Validar email e senha do administrador
            if (file_exists($admin_email_file)) {
                $admin_email_data = file_get_contents($admin_email_file);
                $admin_email_info = json_decode($admin_email_data, true);
                $admin_email = $admin_email_info['email'];
                $admin_password = $admin_email_info['senha'];

                if ($email === $admin_email && $senha === $admin_password) {
                    $response['status'] = 'success';
                    $response['message'] = 'Login do administrador realizado com sucesso';
                    $response['redirect'] = 'admin_dashboard.html';
                    echo json_encode($response);
                    exit;
                } else {
                    $response['message'] = 'Email ou senha do administrador inválidos';
                }
            } else {
                $response['message'] = 'Arquivo de email do administrador não encontrado';
                error_log("Admin email file not found", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            }
        } elseif ($action == 'get_users') {
            // Carregar dados dos usuários
            if (file_exists($json_file)) {
                $json_data = file_get_contents($json_file);
                $usuarios = json_decode($json_data, true);
                if (!is_array($usuarios)) {
                    $usuarios = [];
                }
                $response['status'] = 'success';
                $response['usuarios'] = $usuarios;
            } else {
                $response['message'] = 'Arquivo de usuários não encontrado';
                error_log("User file not found", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            }
        } elseif ($action == 'cadastro') {
            $novo_usuario = $_POST['novo_usuario'];
            $nova_senha = $_POST['nova_senha'];
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];
            error_log("Cadastro attempt: usuario=$novo_usuario", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Carregar dados existentes
            if (file_exists($json_file)) {
                $json_data = file_get_contents($json_file);
                $usuarios = json_decode($json_data, true);
                if (!is_array($usuarios)) {
                    $usuarios = [];
                }
            } else {
                $usuarios = [];
            }

            // Verificar se o usuário já existe
            foreach ($usuarios as $user) {
                if ($user['usuario'] == $novo_usuario) {
                    $response['message'] = 'Usuário já existe';
                    echo json_encode($response);
                    exit;
                }
            }

            // Adicionar novo usuário
            $usuarios[] = [
                'usuario' => $novo_usuario, 
                'senha' => password_hash($nova_senha, PASSWORD_DEFAULT),
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone,
                'quiz_result' => null // Adicionar o campo quiz_result
            ];

            // Salvar dados atualizados
            if (file_put_contents($json_file, json_encode($usuarios)) !== false) {
                $response['status'] = 'success';
                $response['message'] = 'Cadastro realizado com sucesso';

                // Enviar email ao administrador
                if (file_exists($admin_email_file)) {
                    $admin_email_data = file_get_contents($admin_email_file);
                    $admin_email_info = json_decode($admin_email_data, true);
                    $admin_email = $admin_email_info['email'];
                    $admin_password = $admin_email_info['senha'];

                    if ($admin_email && $admin_password) {
                        $mail = new PHPMailer(true);
                        try {
                            // Configurações do servidor
                            $mail->isSMTP();
                            $mail->Host = 'smtp.gmail.com';
                            $mail->SMTPAuth = true;
                            $mail->Username = $admin_email;
                            $mail->Password = $admin_password;
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Port = 587;

                            // Recipientes
                            $mail->setFrom($admin_email, 'Sistema de Cadastro');
                            $mail->addAddress($admin_email);

                            // Conteúdo do e-mail
                            $mail->isHTML(true);
                            $mail->Subject = 'Novo Cadastro de Usuário';
                            $mail->Body = "Um novo usuário foi cadastrado:<br>Usuário: $novo_usuario";

                            $mail->send();
                        } catch (Exception $e) {
                            error_log("Mailer Error: {$mail->ErrorInfo}", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                        }
                    }
                }
            } else {
                $response['message'] = 'Falha ao salvar os dados';
                error_log("Failed to save user data", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            }
        } elseif ($action == 'contato') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $mensagem = $_POST['mensagem'];
            error_log("Contato attempt: nome=$nome, email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            if (file_exists($admin_email_file)) {
                $admin_email_data = file_get_contents($admin_email_file);
                $admin_email_info = json_decode($admin_email_data, true);
                $admin_email = $admin_email_info['email'];
                $admin_password = $admin_email_info['senha'];

                if ($admin_email && $admin_password) {
                    $mail = new PHPMailer(true);
                    try {
                        // Configurações do servidor
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = $admin_email;
                        $mail->Password = $admin_password;
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        // Recipientes
                        $mail->setFrom($email, $nome);
                        $mail->addAddress($admin_email, 'Administrador');

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
            } else {
                $response['message'] = 'Arquivo de configuração de email do administrador não encontrado.';
                error_log("Admin email file not found", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            }
        } elseif ($action == 'cadastrar_email_admin') {
            $email = $_POST['email'];
            $senha = $_POST['senha'];
            error_log("Cadastro email admin attempt: email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            $admin_email_data = json_encode(['email' => $email, 'senha' => $senha]);

            if (file_put_contents($admin_email_file, $admin_email_data) !== false) {
                $response['status'] = 'success';
                $response['message'] = 'Email do administrador cadastrado com sucesso';
            } else {
                $response['message'] = 'Falha ao salvar o email do administrador';
                error_log("Failed to save admin email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            }
        } elseif ($action == 'save_quiz_result') {
            $userEmail = $input['user'];
            $score = $input['score'];
            $users = getUsers();
            $userFound = false;
            foreach ($users as &$user) {
                if ($user['email'] === $userEmail) {
                    $user['quiz_result'] = $score;
                    $userFound = true;
                    break;
                }
            }
            if ($userFound) {
                saveUsers($users);
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

// Funções auxiliares
function getUsers() {
    $filePath = 'usuarios.json';
    if (file_exists($filePath)) {
        $jsonContent = file_get_contents($filePath);
        return json_decode($jsonContent, true);
    }
    return [];
}

function saveUsers($users) {
    $filePath = 'usuarios.json';
    $jsonContent = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents($filePath, $jsonContent);
}

?>
