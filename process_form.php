<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';

    // Dados do usuário
    $userData = [
        'nome' => $nome,
        'email' => $email,
        'telefone' => $telefone,
    ];

    // Caminho do arquivo JSON
    $dataFile = 'users_data.json';

    // Ler os dados existentes
    if (!file_exists($dataFile)) {
        file_put_contents($dataFile, json_encode([]));
    }
    $json = file_get_contents($dataFile);
    $usersData = json_decode($json, true);

    // Adicionar novos dados
    $usersData[] = $userData;
    file_put_contents($dataFile, json_encode($usersData, JSON_PRETTY_PRINT));

    echo "Dados enviados com sucesso!";
} else {
    echo "Método de requisição inválido.";
}
?>
