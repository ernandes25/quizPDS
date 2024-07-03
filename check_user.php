<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $usuario = $data['usuario'] ?? '';
    $senha = $data['senha'] ?? '';

    $json_file = '/opt/lampp/htdocs/quizPDS/usuarios.json';

    if (file_exists($json_file)) {
        $usuarios = json_decode(file_get_contents($json_file), true);
        foreach ($usuarios as $user) {
            if ($user['usuario'] === $usuario && password_verify($senha, $user['senha'])) {
                echo json_encode(['status' => 'success', 'message' => 'Login realizado com sucesso.']);
                exit;
            }
        }
    }

    echo json_encode(['status' => 'error', 'message' => 'Usuário ou senha inválidos.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método de requisição inválido.']);
}
?>
