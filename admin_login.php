<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminPassword = $_POST['adminPassword'] ?? '';

    // Substitua pela senha real que seu cliente usará
    $correctPassword = '1234';

    if ($adminPassword === $correctPassword) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_dashboard.php');
        exit();
    } else {
        echo "Senha incorreta.";
    }
} else {
    echo "Método de requisição inválido.";
}
?>
