<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.'];

// Manipulador de erros
set_error_handler(function($severity, $message, $file, $line) {
    echo json_encode([
        'status' => 'error',
        'message' => $message,
        'file' => $file,
        'line' => $line
    ]);
    exit;
});

// Manipulador de exceções
set_exception_handler(function($exception) {
    echo json_encode([
        'status' => 'error',
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine()
    ]);
    exit;
});

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $action = $_POST['action'];
        $json_file = '/opt/lampp/htdocs/quizPDS/usuarios.json';
        $admin_email_file = '/opt/lampp/htdocs/quizPDS/admin_email.json';

        if (!is_readable($json_file) || !is_writable($json_file)) {
            throw new Exception('Arquivo de usuários não está acessível.');
        }

        if (!is_readable($admin_email_file) || !is_writable($admin_email_file)) {
            throw new Exception('Arquivo de email do administrador não está acessível.');
        }

        if ($action == 'login') {
            $usuario = $_POST['usuario'] ?? '';
            $senha = $_POST['senha'] ?? '';

            if (empty($usuario) || empty($senha)) {
                throw new Exception('Usuário e senha são obrigatórios.');
            }

            $usuarios = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];
            if (!is_array($usuarios)) {
                $usuarios = [];
            }

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
        } elseif ($action == 'cadastro') {
            $novo_usuario = $_POST['novo_usuario'] ?? '';
            $nova_senha = $_POST['nova_senha'] ?? '';
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $telefone = $_POST['telefone'] ?? '';

            if (empty($novo_usuario) || empty($nova_senha) || empty($nome) || empty($email) || empty($telefone)) {
                throw new Exception('Todos os campos são obrigatórios.');
            }

            $usuarios = file_exists($json_file) ? json_decode(file_get_contents($json_file), true) : [];
            if (!is_array($usuarios)) {
                $usuarios = [];
            }

            foreach ($usuarios as $user) {
                if ($user['usuario'] == $novo_usuario) {
                    $response['message'] = 'Usuário já existe';
                    echo json_encode($response);
                    exit;
                }
            }

            $usuarios[] = [
                'usuario' => $novo_usuario,
                'senha' => password_hash($nova_senha, PASSWORD_DEFAULT),
                'nome' => $nome,
                'email' => $email,
                'telefone' => $telefone
            ];

            if (file_put_contents($json_file, json_encode($usuarios))) {
                sendAdminNotification($novo_usuario, $nome, $email, $telefone);
                $response['status'] = 'success';
                $response['message'] = 'Cadastro realizado com sucesso';
                $response['redirect'] = 'quiz.html';
            } else {
                throw new Exception('Falha ao salvar os dados');
            }
        } elseif ($action == 'contato') {
            $nome = $_POST['nome'] ?? '';
            $email = $_POST['email'] ?? '';
            $mensagem = $_POST['mensagem'] ?? '';

            if (empty($nome) || empty($email) || empty($mensagem)) {
                throw new Exception('Todos os campos são obrigatórios.');
            }

            sendContactEmail($nome, $email, $mensagem);
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    echo json_encode($response);
    exit;
}

echo json_encode($response);

function sendAdminNotification($novo_usuario, $nome, $email, $telefone) {
    global $admin_email_file, $response;
    if (file_exists($admin_email_file)) {
        $admin_email_data = json_decode(file_get_contents($admin_email_file), true);

        if (isset($admin_email_data['email']) && isset($admin_email_data['senha'])) {
            $admin_email = $admin_email_data['email'];
            $admin_password = $admin_email_data['senha'];

            if ($admin_email && $admin_password) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $admin_email;
                    $mail->Password = $admin_password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom($admin_email, 'Sistema de Cadastro');
                    $mail->addAddress($admin_email);

                    $mail->isHTML(true);
                    $mail->Subject = 'Novo Cadastro de Usuário';
                    $mail->Body = "Um novo usuário foi cadastrado:<br>Usuário: $novo_usuario<br>Nome: $nome<br>Email: $email<br>Telefone: $telefone";

                    $mail->send();
                } catch (Exception $e) {
                    throw new Exception("A mensagem não pôde ser enviada. Erro: {$mail->ErrorInfo}");
                }
            }
        } else {
            throw new Exception('Configuração de email do administrador inválida.');
        }
    } else {
        throw new Exception('Arquivo de configuração de email do administrador não encontrado.');
    }
}

function sendContactEmail($nome, $email, $mensagem) {
    global $admin_email_file, $response;
    if (file_exists($admin_email_file)) {
        $admin_email_data = json_decode(file_get_contents($admin_email_file), true);

        if (isset($admin_email_data['email']) && isset($admin_email_data['senha'])) {
            $admin_email = $admin_email_data['email'];
            $admin_password = $admin_email_data['senha'];

            if ($admin_email && $admin_password) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $admin_email;
                    $mail->Password = $admin_password;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom($email, $nome);
                    $mail->addAddress($admin_email, 'Administrador');

                    $mail->isHTML(true);
                    $mail->Subject = 'Novo Contato do Site';
                    $mail->Body = "Nome: $nome<br>Email: $email<br>Mensagem: $mensagem";

                    $mail->send();
                    $response['status'] = 'success';
                    $response['message'] = 'Mensagem enviada com sucesso.';
                } catch (Exception $e) {
                    throw new Exception("A mensagem não pôde ser enviada. Erro: {$mail->ErrorInfo}");
                }
            } else {
                throw new Exception('Configuração de email do administrador inválida.');
            }
        } else {
            throw new Exception('Arquivo de configuração de email do administrador não encontrado.');
        }
    }
}
?>
