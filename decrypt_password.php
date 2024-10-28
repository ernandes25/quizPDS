<?php

// Definir a chave de criptografia
define('SECRET_KEY', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'); // Substitua pela sua chave secreta real

// Função para descriptografar a senha
function decryptPassword($encryptedPassword) {
    $iv = '1234567891011121'; // Vetor de inicialização
    $encrypted = base64_decode($encryptedPassword); // Decodifica de Base64 antes da descriptografia
    return openssl_decrypt($encrypted, 'AES-128-CTR', SECRET_KEY, 0, $iv);
}

// Conexão com o banco de dados
require 'db_config.php';

// Email do administrador para o qual você deseja descriptografar a senha
$emailAdmin = 'contato@ercont.com.br';

// Recuperar a senha criptografada do banco de dados
$stmt = $pdo->prepare("SELECT senha FROM admin_emails WHERE email = ?");
$stmt->execute([$emailAdmin]);
$admin = $stmt->fetch();

if ($admin) {
    $senhaCriptografada = $admin['senha'];
    echo "Senha criptografada do banco de dados: " . $senhaCriptografada . "<br>";
    $senhaDescriptografada = decryptPassword($senhaCriptografada);
    echo "Senha descriptografada: " . $senhaDescriptografada;
} else {
    echo "Administrador não encontrado.";
}

?>