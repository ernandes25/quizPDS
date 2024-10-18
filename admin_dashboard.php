<?php
session_start();

// Verifica se o usuário está logado como administrador
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: admin_login.php');
    exit();
}

// Inclui a configuração do banco de dados
require 'db_config.php';

// Consulta para buscar dados dos usuários e resultados do quiz
$stmt = $pdo->query("
    SELECT 
        id,
        nome, 
        email, 
        telefone, 
        resultado_quiz, 
        data_hora_quiz
    FROM 
        usuarios
    ORDER BY 
        data_hora_quiz DESC
");

// Recupera todos os dados em um array associativo
$usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Função para formatar a data
function formatDate($date) {
    if(!$date) return 'N/A';
    $timestamp = strtotime($date);if (!$date) return 'N/A';
        $timestamp = strtotime($date);
        return $timestamp ? date("d m Y H:i", $timestamp) : 'Data inválida';
    return $timestamp ? date("d m Y H:i", $timestamp($date)) : 'Data inválida';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main id="main-admin-dashboard">
        <h1>Painel do Administrador</h1>

        <ul>
            <span id="user-greeting">Bem-vindo, <?php echo htmlspecialchars($_SESSION['admin_email']); ?>!</span>
        </ul>
        
        <table id="users-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Resultado do Quiz</th>
                    <th>Data e Hora do Quiz</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usersData as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['nome']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['telefone']); ?></td>
                        <td><?php echo htmlspecialchars($user['resultado_quiz'] ?? 'N/A'); ?></td>
                        <td><?php echo formatDate($user['data_hora_quiz']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button id="logout-button-main">Sair</button>
    </main>

    <footer>
        <p>&copy; 2024 Seu Site. Todos os direitos reservados.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>