<?php

function generateUserToken($userId, $secretKey) {
    // Cria um timestamp para evitar reutilização de tokens
    $timestamp = time();

    // Concatena o ID do usuário e o timestamp
    $data = $userId . '|' . $timestamp;

    // Gera o token usando HMAC e SHA256
    $token = hash_hmac('sha256', $data, $secretKey);

    // Retorna o token
    return $token;
}

// Exemplo de uso
$userId = 123;
$secretKey = 'sua_chave_secreta';

$userToken = generateUserToken($userId, $secretKey);
echo "Token do usuário: " . $userToken;

?>