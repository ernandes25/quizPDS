<?php

// Definir a chave de criptografia
define('SECRET_KEY', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'); // Substitua pela sua chave secreta real

// Função para criptografar a senha
function encryptPassword($password) {
    $iv = '1234567891011121'; // Vetor de inicialização
    $encrypted = openssl_encrypt($password, 'AES-128-CTR', SECRET_KEY, 0, $iv);
    return base64_encode($encrypted); // Codifica o resultado em Base64
}

// Conexão com o banco de dados
require 'db_config.php';

// Exemplo de uso
$senha = '250200er25*'; // Substitua pela senha que você deseja criptografar
$senhaCriptografada = encryptPassword($senha);
echo "Senha criptografada: " . $senhaCriptografada . "<br>";

// Armazenar a senha criptografada no banco de dados
$emailAdmin = 'contato@ercont.com.br';
$stmt = $pdo->prepare("UPDATE admin_emails SET senha = ? WHERE email = ?");
$stmt->execute([$senhaCriptografada, $emailAdmin]);

echo "Senha armazenada no banco de dados.";

?>