<?php
// Define o cabeçalho para JSON
header('Content-Type: application/json');

// Inicia a sessão
session_start();

// Registro dos logs para depuração
$logFile = 'log.txt';
function writeLog($message) {
    global $logFile;
    $time = date('Y-m-d H:i:s');
    $logMessage = "[$time] - $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Verifica se os dados de cadastro foram recebidos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    writeLog("Dados POST recebidos: " . var_export($_POST, true));

    if (!isset($_POST['nome']) || !isset($_POST['email']) || !isset($_POST['telefone']) || !isset($_POST['usuario']) || !isset($_POST['senha'])) {
        echo json_encode(['status' => 'error', 'message' => 'Dados de cadastro incompletos.']);
        writeLog('Dados de cadastro incompletos.');
        exit;
    }

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $telefone = $_POST['telefone'];
    $usuario = $_POST['usuario'];
    $senha = $_POST['senha'];

    // Lê os dados do arquivo JSON de usuários
    $filename = 'users_data.json';
    if (file_exists($filename)) {
        $usersData = json_decode(file_get_contents($filename), true);
    } else {
        $usersData = [];
    }

    // Adiciona o novo usuário
    $usersData[] = [
        'nome' => $nome,
        'email' => $email,
        'telefone' => $telefone,
        'usuario' => $usuario,
        'senha' => $senha
    ];

    // Salva os dados no arquivo JSON
    file_put_contents($filename, json_encode($usersData));
    writeLog('Dados armazenados com sucesso.');

    echo json_encode(['status' => 'success', 'message' => 'Cadastro bem-sucedido. Redirecionando para a página de quiz...']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de solicitação inválido.']);
    writeLog('Método de solicitação inválido.');
}
?>
