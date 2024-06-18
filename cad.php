<?php
session_start();

header('Content-Type: application/json');

// Função para ler os dados do arquivo JSON
function readUsers() {
    $usersFile = 'users.json';
    if (!file_exists($usersFile)) {
        file_put_contents($usersFile, json_encode([]));
    }
    $json = file_get_contents($usersFile);
    return json_decode($json, true);
}

// Função para escrever os dados no arquivo JSON
function writeUsers($users) {
    $json = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents('users.json', $json);
}

// Função para registrar logs
function writeLog($message) {
    file_put_contents('log.txt', date('Y-m-d H:i:s') . " - " . $message . PHP_EOL, FILE_APPEND);
}

function returnError($message) {
    writeLog($message);
    echo json_encode(['status' => 'error', 'message' => $message]);
    exit;
}

writeLog("Arquivo cad.php acessado.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $senha = $_POST['senha'] ?? '';
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    writeLog("Recebido POST: usuario=$usuario, senha=$senha, nome=$nome, email=$email, telefone=$telefone");

    $users = readUsers();
    writeLog("Usuários lidos: " . json_encode($users));

    if (!empty($usuario) && !empty($senha)) {
        $userFound = false;
        foreach ($users as $user) {
            if ($user['usuario'] === $usuario) {
                $userFound = true;
                if (password_verify($senha, $user['senha'])) {
                    $_SESSION['user'] = $usuario;
                    writeLog("Login bem-sucedido para o usuário: $usuario");
                    echo json_encode(['status' => 'success', 'message' => 'Login bem-sucedido']);
                    exit;
                } else {
                    returnError("Senha incorreta para o usuário: $usuario");
                }
            }
        }

        if (!$userFound) {
            if (!empty($nome) && !empty($email)) {
                $hashed_password = password_hash($senha, PASSWORD_DEFAULT);
                $newUser = [
                    'nome' => $nome,
                    'email' => $email,
                    'telefone' => $telefone,
                    'usuario' => $usuario,
                    'senha' => $hashed_password
                ];
                $users[] = $newUser;
                writeUsers($users);
                $_SESSION['user'] = $usuario;
                writeLog("Cadastro e login bem-sucedidos para o usuário: $usuario");
                echo json_encode(['status' => 'success', 'message' => 'Cadastro e login bem-sucedidos']);
                exit;
            } else {
                returnError("Usuário não encontrado e dados de cadastro incompletos");
            }
        }
    } else {
        returnError("Usuário e senha são obrigatórios");
    }
} else {
    returnError("Método de requisição não é POST.");
}
?>
