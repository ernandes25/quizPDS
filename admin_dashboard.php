<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.html');
    exit();
}

// Ler os dados do arquivo JSON
$dataFile = 'users_data.json';
if (!file_exists($dataFile)) {
    echo "Nenhum dado encontrado.";
    exit();
}
$json = file_get_contents($dataFile);
$usersData = json_decode($json, true);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard de Administração</title>
</head>
<body>
    <h1>Dados dos Usuários</h1>
    <table border="1">
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>Telefone</th>
        </tr>
        <?php foreach ($usersData as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['nome']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['telefone']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="admin_logout.php">Sair</a>
</body>
</html>
