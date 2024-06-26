<?php
session_start();

function logError($message) {
    $logFile = 'error_log.txt';
    $currentTime = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$currentTime] - $message\n", FILE_APPEND);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Logar o array $_POST completo
        logError("Conteúdo completo de \$_POST: " . print_r($_POST, true));

        // Verificar a existência das chaves no POST
        if (!isset($_POST['usuario']) || !isset($_POST['senha'])) {
            logError('Usuário ou senha não fornecidos no POST.');
            throw new Exception('Usuário ou senha não fornecidos no POST.');
        }

        $usuario = $_POST['usuario'];
        $senha = $_POST['senha'];

        logError("Usuário: $usuario, Senha: $senha");

        $usersFile = 'users_data.json';
        if (!file_exists($usersFile)) {
            file_put_contents($usersFile, json_encode([]));
        }
        $usersData = json_decode(file_get_contents($usersFile), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            logError('Erro ao decodificar JSON: ' . json_last_error_msg());
            throw new Exception('Erro ao decodificar JSON');
        }

        $userFound = false;
        foreach ($usersData as $user) {
            if (isset($user['usuario']) && $user['usuario'] === $usuario && isset($user['senha']) && $user['senha'] === $senha) {
                $userFound = true;
                $_SESSION['logged_in'] = true;
                $_SESSION['usuario'] = $usuario;
                break;
            }
        }

        if ($userFound) {
            $response = ['status' => 'success', 'message' => 'Login bem-sucedido'];
        } else {
            $response = ['status' => 'user_not_found', 'message' => 'Usuário não encontrado, redirecionando para cadastro'];
        }

        logError("Resposta JSON: " . json_encode($response));

        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        logError('Método de requisição inválido.');
        throw new Exception('Método de requisição inválido.');
    }
} catch (Exception $e) {
    logError($e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao processar sua solicitação. Por favor, tente novamente mais tarde.']);
    http_response_code(500);
}
?>