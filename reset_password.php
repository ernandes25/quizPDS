<?php
require 'db_config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $nova_senha = $_POST['nova_senha'];

    // Verificar token e expiração
    $stmt = $pdo->prepare("SELECT * FROM admin_emails WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $admin = $stmt->fetch();

    if ($admin) {
        // Atualizar senha
        $hashedPassword = password_hash($nova_senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE admin_emails SET senha = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $stmt->execute([$hashedPassword, $token]);
        echo "Senha redefinida com sucesso.";
    } else {
        echo "Token inválido ou expirado.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
</head>
<body>
    <form method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
        <label for="nova_senha">Nova Senha:</label>
        <input type="password" id="nova_senha" name="nova_senha" required>
        <button type="submit">Redefinir Senha</button>
    </form>
</body>
</html>