<?php
header('Content-Type: application/json');

$usersFilePath = 'users_data.json';

$nome = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$telefone = $_POST['telefone'] ?? null;
$usuario = $_POST['usuario'] ?? null;
$senha = $_POST['senha'] ?? null;

if (!$nome || !$email || !$telefone || !$usuario || !$senha) {
    echo json_encode(["status" => "error", "message" => "Todos os campos são necessários."]);
    exit;
}

$newUser = [
    'nome' => $nome,
    'email' => $email,
    'telefone' => $telefone,
    'usuario' => $usuario,
    'senha' => password_hash($senha, PASSWORD_BCRYPT),
];

$usersData = [];
if (file_exists($usersFilePath)) {
    $usersData = json_decode(file_get_contents($usersFilePath), true);
}

$usersData[] = $newUser;

if (file_put_contents($usersFilePath, json_encode($usersData))) {
    echo json_encode(["status" => "success", "message" => "Usuário cadastrado com sucesso."]);
} else {
    echo json_encode(["status" => "error", "message" => "Falha ao salvar os dados do usuário."]);
}
?>
