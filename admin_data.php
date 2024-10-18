<?php
session_start();

// Verifique se o administrador está logado
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.html');
    exit;
}

$usersFilePath = 'users_data.json';

$usersData = [];
if (file_exists($usersFilePath)) {
    $usersData = json_decode(file_get_contents($usersFilePath), true);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dados dos Usuários</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <img id="logo" src="logo.jpeg" alt="Logo" />
            </ul>
            <ul>
                <a class="item" href="index.html">
                    <li>Início</li>
                </a>
            </ul>
            <ul>
                <a class="item" href="admin_dashboard.php">
                    <li>Painel do Administrador</li>
                </a>
            </ul>
            <ul>
                <a class="item" href="admin_logout.php">
                    <li>Sair</li>
                </a>
            </ul>
        </nav>
    </header>
    <h1>Dados dos Usuários</h1>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Usuário</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usersData as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['nome']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($user['usuario']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
