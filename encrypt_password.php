<?php

// Definir a chave de criptografia
define('SECRET_KEY', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'); // Substitua pela sua chave secreta real

// Função para criptografar a senha
function encryptPassword($password) {
    $iv = '1234567891011121'; // Vetor de inicialização
    $encrypted = openssl_encrypt($password, 'AES-128-CTR', SECRET_KEY, 0, $iv);
    return base64_encode($encrypted); // Codifica o resultado em Base64
}

// Exemplo de uso
$senha = ''; // Substitua pela senha que você deseja criptografar
$senhaCriptografada = encryptPassword($senha);
echo "Senha criptografada: " . $senhaCriptografada;

?>