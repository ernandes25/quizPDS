<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'db_config.php';

session_start();

// Definir a chave de criptografia
define('SECRET_KEY', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'); // Armazene essa chave de forma segura e não compartilhe

// Função para criptografar a senha
function encryptPassword($password)
{
    $encrypted = openssl_encrypt($password, 'AES-128-CTR', SECRET_KEY, 0, '1234567891011121');
    return base64_encode($encrypted); // Codifica o resultado em Base64
}

// Função para descriptografar a senha
function decryptPassword($encryptedPassword)
{
    $encrypted = base64_decode($encryptedPassword); // Decodifica de Base64 antes da descriptografia
    return openssl_decrypt($encrypted, 'AES-128-CTR', SECRET_KEY, 0, '1234567891011121');
}

$response = ['status' => 'error', 'message' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente mais tarde.'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    error_log("Solicitação POST recebida", 3, "/opt/lampp/htdocs/quizPDS/error.log");
    $action = $_POST['action'] ?? '';
    error_log("Ação recebida: $action", 3, "/opt/lampp/htdocs/quizPDS/error.log");

    if ($action == 'recuperar_senha') {
        $email = $_POST['email'];
        error_log("Tentativa de recuperação de senha: email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

        // Verificar se o email está registrado
        $stmt = $pdo->prepare("SELECT * FROM admin_emails WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin) {
            error_log("Email encontrado: $email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Gerar token de recuperação de senha
            $token = bin2hex(random_bytes(50));
            $stmt = $pdo->prepare("UPDATE admin_emails SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
            if ($stmt->execute([$token, $email])) {
                error_log("Token gerado: $token", 3, "/opt/lampp/htdocs/quizPDS/error.log");

                // Buscar credenciais de email do banco de dados
                $stmt = $pdo->query("SELECT email, senha FROM admin_emails LIMIT 1");
                $emailConfig = $stmt->fetch();

                if ($emailConfig) {
                    // Descriptografar a senha antes de usar no PHPMailer
                    $senhaDescriptografada = decryptPassword($emailConfig['senha']);
                    error_log("Senha descriptografada: $senhaDescriptografada", 3, "/opt/lampp/htdocs/quizPDS/error.log");

                    // Enviar email com link de redefinição de senha
                    $resetLink = "http://localhost/quizPDS/reset_password.php?token=$token";
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = $emailConfig['email'];
                        $mail->Password = $senhaDescriptografada; // Usar a senha descriptografada
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        error_log("Configurando PHPMailer", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                        $mail->setFrom($emailConfig['email'], 'Sistema de Recuperação de Senha');
                        $mail->addAddress($email);
                        error_log("Destinatário adicionado: $email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

                        $mail->isHTML(true);
                        $mail->Subject = 'Redefinição de Senha';
                        $mail->Body = "Clique no link para redefinir sua senha: <a href='$resetLink'>$resetLink</a>";
                        error_log("Conteúdo do e-mail configurado", 3, "/opt/lampp/htdocs/quizPDS/error.log");

                        $mail->send();
                        error_log("Email enviado para: $email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                        $response['status'] = 'success';
                        $response['message'] = 'Email de recuperação de senha enviado com sucesso.';
                    } catch (Exception $e) {
                        error_log("Erro no envio do email: {$mail->ErrorInfo}", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                        $response['message'] = "A mensagem não pôde ser enviada. Erro: {$mail->ErrorInfo}";
                    }
                } else {
                    error_log("Falha ao buscar credenciais de email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                    $response['message'] = 'Falha ao buscar credenciais de email';
                }
            } else {
                error_log("Falha ao atualizar o token no banco de dados", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                $response['message'] = 'Falha ao atualizar o token no banco de dados';
            }
        } else {
            error_log("Email não encontrado: $email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            $response['message'] = 'Email não encontrado.';
        }
        echo json_encode($response);
        exit;
    } elseif ($action == 'recuperar_senha_usuario') {
        $email = $_POST['email'];
        error_log("Tentativa de recuperação de senha de usuário: email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

        // Verificar se o email está registrado
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            error_log("Email encontrado: $email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Gerar token de recuperação de senha
            $token = bin2hex(random_bytes(50));
            $stmt = $pdo->prepare("UPDATE usuarios SET reset_token = ?, reset_token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
            if ($stmt->execute([$token, $email])) {
                error_log("Token gerado: $token", 3, "/opt/lampp/htdocs/quizPDS/error.log");

                // Buscar credenciais de email do administrador
                $stmt = $pdo->query("SELECT email, senha FROM admin_emails LIMIT 1");
                $emailConfig = $stmt->fetch();

                if ($emailConfig) {
                    // Descriptografar a senha antes de usar no PHPMailer
                    $senhaDescriptografada = decryptPassword($emailConfig['senha']);
                    error_log("Senha descriptografada: $senhaDescriptografada", 3, "/opt/lampp/htdocs/quizPDS/error.log");

                    // Enviar email com link de redefinição de senha
                    $resetLink = "http://localhost/quizPDS/reset_password.php?token=$token";
                    $mail = new PHPMailer(true);
                    try {
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = $emailConfig['email'];
                        $mail->Password = $senhaDescriptografada; // Usar a senha descriptografada
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        error_log("Configurando PHPMailer", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                        $mail->setFrom($emailConfig['email'], 'Sistema de Recuperação de Senha');
                        $mail->addAddress($email);
                        error_log("Destinatário adicionado: $email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

                        $mail->isHTML(true);
                        $mail->Subject = 'Redefinição de Senha';
                        $mail->Body = "Clique no link para redefinir sua senha: <a href='$resetLink'>$resetLink</a>";
                        error_log("Conteúdo do e-mail configurado", 3, "/opt/lampp/htdocs/quizPDS/error.log");

                        $mail->send();
                        error_log("Email enviado para: $email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                        $response['status'] = 'success';
                        $response['message'] = 'Email de recuperação de senha enviado com sucesso.';
                    } catch (Exception $e) {
                        error_log("Erro no envio do email: {$mail->ErrorInfo}", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                        $response['message'] = "A mensagem não pôde ser enviada. Erro: {$mail->ErrorInfo}";
                    }
                } else {
                    error_log("Falha ao buscar credenciais de email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                    $response['message'] = 'Falha ao buscar credenciais de email';
                }
            } else {
                error_log("Falha ao atualizar o token no banco de dados", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                $response['message'] = 'Falha ao atualizar o token no banco de dados';
            }
        } else {
            error_log("Email não encontrado: $email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            $response['message'] = 'Email não encontrado.';
        }
        echo json_encode($response);
        exit;
    } elseif ($action == 'admin_login') {
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        error_log("Tentativa de login do admin: email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

        // Validar email e senha do administrador
        $stmt = $pdo->prepare("SELECT * FROM admin_emails WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        if ($admin) {
            // Descriptografar a senha armazenada
            $senhaDescriptografada = decryptPassword($admin['senha']);
            if ($senha === $senhaDescriptografada) {
                // Login bem-sucedido
                $_SESSION['admin'] = $admin['email'];
                $response['status'] = 'success';
                $response['message'] = 'Login do administrador realizado com sucesso';
                $response['redirect'] = 'admin_dashboard.html';
            } else {
                // Falha no login
                $response['message'] = 'Email ou senha do administrador inválidos';
            }
        } else {
            // Falha no login
            $response['message'] = 'Email ou senha do administrador inválidos';
        }
        echo json_encode($response);
        exit;
    } elseif ($action == 'cadastrar_email_admin') {
        // Verificar se já existe algum administrador cadastrado
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin_emails");
        $adminCount = $stmt->fetchColumn();

        if ($adminCount > 0 && !isset($_SESSION['admin'])) {
            $response['status'] = 'error';
            $response['message'] = 'Acesso negado. Faça login como administrador.';
            echo json_encode($response);
            exit;
        }

        $email = $_POST['email'];
        $senha = $_POST['senha'];
        error_log("Tentativa de cadastro de email admin: email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

        // Verificar se o email já existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_emails WHERE email = ?");
        $stmt->execute([$email]);
        $emailCount = $stmt->fetchColumn();

        if ($emailCount > 0) {
            $response['status'] = 'error';
            $response['message'] = 'Este email de administrador já está cadastrado.';
            echo json_encode($response);
            exit;
        }

        // Criptografar a senha
        $senhaCriptografada = encryptPassword($senha);

        $stmt = $pdo->prepare("INSERT INTO admin_emails (email, senha) VALUES (?, ?)");
        if ($stmt->execute([$email, $senhaCriptografada])) {
            $response['status'] = 'success';
            $response['message'] = 'Email do administrador cadastrado com sucesso';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Falha ao salvar o email do administrador';
            error_log("Failed to save admin email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
        }
        echo json_encode($response);
        exit;
    }
}

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente mais tarde.'];
// Defina um manipulador de erros para capturar todos os erros e emitir JSON
set_error_handler(function ($severity, $message, $file, $line) {
    http_response_code(500);
    error_log("Erro: [$severity] $message in $file on line $line", 3, "/opt/lampp/htdocs/quizPDS/error.log");
    echo json_encode([
        'status' => 'error',
        'message' => $message,
        'file' => $file,
        'line' => $line
    ]);
    exit;
});

set_exception_handler(function ($exception) {
    http_response_code(500);
    error_log("Exceção: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine(), 3, "/opt/lampp/htdocs/quizPDS/error.log");
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
    error_log("Solicitação recebida: " . json_encode($_POST), 3, "/opt/lampp/htdocs/quizPDS/error.log");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        // Determine se estamos lidando com JSON ou dados de formulário
        if ($input) {
            error_log('JSON decodificado: ' . print_r($input, true), 3, "/opt/lampp/htdocs/quizPDS/error.log"); // Adicionado para depuração
            $action = $input['action'] ?? '';
        } else {
            error_log('Dados de formulário recebidos: ' . print_r($_POST, true), 3, "/opt/lampp/htdocs/quizPDS/error.log"); // Adicionado para depuração
            $action = $_POST['action'] ?? '';
        }

        error_log("Ação: $action", 3, "/opt/lampp/htdocs/quizPDS/error.log");

        if ($action == 'login') {
            $usuario = $_POST['usuario'];
            $senha = $_POST['senha'];
            error_log("Tentativa de login: usuario=$usuario", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Validar usuário e senha
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ?");
            $stmt->execute([$usuario]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($senha, $user['senha'])) {
                    $response['status'] = 'success';
                    $response['message'] = 'Login realizado com sucesso';
                    $response['redirect'] = 'quiz.html';
                } else {
                    $response['status'] = 'error';
                    $response['message'] = 'Usuário e/ou senha incorretos.';
                }
            } else {
                $response['status'] = 'user_not_found';
                $response['message'] = 'Usuário não encontrado. Deseja efetuar seu cadastro agora?';
                $response['redirect'] = 'cadastro.html';
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
            error_log("Tentativa de cadastro: usuario=$novo_usuario", 3, "/opt/lampp/htdocs/quizPDS/error.log");

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
                        $mail->Password = decryptPassword($admin['senha']); // Descriptografar a senha
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
                        error_log("Erro no envio do email: {$mail->ErrorInfo}", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                    }
                }
            }
            echo json_encode($response);
            exit;
        } elseif ($action == 'contato') {
            $nome = $_POST['nome'];
            $email = $_POST['email'];
            $mensagem = $_POST['mensagem'];
            error_log("Tentativa de contato: nome=$nome, email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

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
                    $mail->Password = decryptPassword($admin['senha']); // Descriptografar a senha
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
                    error_log("Erro no envio do email: {$mail->ErrorInfo}", 3, "/opt/lampp/htdocs/quizPDS/error.log");
                }
            } else {
                $response['message'] = 'Configuração de email do administrador inválida.';
            }
            echo json_encode($response);
            exit;
        } elseif ($action == 'cadastrar_email_admin') {
            // Verificar se já existe algum administrador cadastrado
            $stmt = $pdo->query("SELECT COUNT(*) FROM admin_emails");
            $adminCount = $stmt->fetchColumn();

            if ($adminCount > 0 && !isset($_SESSION['admin'])) {
                $response['status'] = 'error';
                $response['message'] = 'Acesso negado. Faça login como administrador.';
                echo json_encode($response);
                exit;
            }

            $email = $_POST['email'];
            $senha = $_POST['senha'];
            error_log("Tentativa de cadastro de email admin: email=$email", 3, "/opt/lampp/htdocs/quizPDS/error.log");

            // Verificar se o email já existe
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_emails WHERE email = ?");
            $stmt->execute([$email]);
            $emailCount = $stmt->fetchColumn();

            if ($emailCount > 0) {
                $response['status'] = 'error';
                $response['message'] = 'Este email de administrador já está cadastrado.';
                echo json_encode($response);
                exit;
            }

            // Criptografar a senha
            $senhaCriptografada = encryptPassword($senha);

            $stmt = $pdo->prepare("INSERT INTO admin_emails (email, senha) VALUES (?, ?)");
            if ($stmt->execute([$email, $senhaCriptografada])) {
                $response['status'] = 'success';
                $response['message'] = 'Email do administrador cadastrado com sucesso';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Falha ao salvar o email do administrador';
                error_log("Failed to save admin email", 3, "/opt/lampp/htdocs/quizPDS/error.log");
            }
            echo json_encode($response);
            exit;
        } elseif ($action == 'save_quiz_result') {
            $userEmail = $input['user'];
            $score = $input['score'];
            $currentDateTime = date('Y-m-d H:i:s'); // Obtém a data e hora atual

            error_log("Salvando resultado do quiz: user=$userEmail, score=$score", 3, "/opt/lampp/htdocs/quizPDS/error.log"); // Adicionado para depuração

            $stmt = $pdo->prepare("UPDATE usuarios SET quiz_result = ?, data_hora_quiz = ? WHERE usuario = ?");
            if ($stmt->execute([$score, $currentDateTime, $userEmail])) {
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
    error_log("Exceção: " . $e->getMessage(), 3, "/opt/lampp/htdocs/quizPDS/error.log");
}

echo json_encode($response);