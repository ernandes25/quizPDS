<?php
require 'db_config.php';

// Definir a chave de criptografia
define('SECRET_KEY', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'); // Substitua pela sua chave secreta real

// Função para criptografar a senha
function encryptPassword($password) {
    $iv = '1234567891011121'; // Vetor de inicialização
    $encrypted = openssl_encrypt($password, 'AES-128-CTR', SECRET_KEY, 0, $iv);
    return base64_encode($encrypted); // Codifica o resultado em Base64
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $nova_senha = $_POST['nova_senha'];

    // Verificar token e expiração
    $stmt = $pdo->prepare("SELECT * FROM admin_emails WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $admin = $stmt->fetch();

    if ($admin) {
        // Criptografar a nova senha
        $senhaCriptografada = encryptPassword($nova_senha);
        $stmt = $pdo->prepare("UPDATE admin_emails SET senha = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $stmt->execute([$senhaCriptografada, $token]);
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