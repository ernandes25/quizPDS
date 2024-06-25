<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adminPassword'])) {
        // Processo de login do administrador
        $adminPassword = $_POST['adminPassword'] ?? '';

        // Substitua pela senha real que seu cliente usará
        $correctPassword = 'senha_do_cliente';

        if ($adminPassword === $correctPassword) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: admin_dashboard.php');
            exit();
        } else {
            echo "Senha incorreta.";
        }
    }
}
?>
