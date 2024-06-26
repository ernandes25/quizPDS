<?php
session_start();

function logError($message) {
    $logFile = 'error_log.txt';
    $currentTime = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$currentTime] - $message\n", FILE_APPEND);
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postData = print_r($_POST, true);
        logError("Dados POST recebidos: $postData");

        $usuario = isset($_POST['usuario']) ? $_POST['usuario'] : '';
        $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

        if (empty($usuario) || empty($senha)) {
            logError('Usuário ou senha não fornecidos.');
            throw new Exception('Usuário ou senha não fornecidos.');
        }

        // Aqui você deve adicionar a lógica para verificar o usuário e senha do administrador
        $adminUser = 'admin';
        $adminPass = 'admin123'; // Substitua isso pela lógica de verificação correta

        if ($usuario === $adminUser && $senha === $adminPass) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_usuario'] = $usuario;
            echo json_encode(['status' => 'success', 'message' => 'Login do administrador bem-sucedido']);
        } else {
            throw new Exception('Usuário ou senha do administrador incorretos.');
        }
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