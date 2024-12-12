<?php
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token'])) {
    $token = $_GET['token'];

    // Verificar se o token é válido e não expirou
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Token válido, exibir formulário para redefinir a senha
        echo '<form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
                <label for="new_password">Nova Senha:</label>
                <input type="password" id="new_password" name="new_password" required>
                <button type="submit">Redefinir Senha</button>
              </form>';
    } else {
        echo 'Token inválido ou expirado.';
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token']) && isset($_POST['new_password'])) {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Atualizar a senha do usuário
    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
    if ($stmt->execute([$new_password, $token])) {
        echo 'Senha redefinida com sucesso.';
    } else {
        echo 'Falha ao redefinir a senha.';
    }
} else {
    echo 'Requisição inválida.';
}
?>