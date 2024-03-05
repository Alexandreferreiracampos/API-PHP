<?php

// Verifica se o token foi enviado nos cabeçalhos
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    http_response_code(401);
    exit(json_encode(array("message" => "Token de autorização não fornecido")));
}

// Obtém o token dos cabeçalhos
$authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
$tokenParts = explode(" ", $authorizationHeader);
$token = isset($tokenParts[1]) ? $tokenParts[1] : null;

// Verifica se o token está presente
if (!$token) {
    http_response_code(401);
    exit(json_encode(array("message" => "Token de autorização inválido")));
}

// resposta da API
http_response_code(200);
echo json_encode(array("message" => $token));

