<?php

require_once("../../vendor/autoload.php");
require_once("../../class/database.class.php");

$con = new Database();
$link = $con->getConexao();
$linkId = $con->getConexao();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


// Chave secreta usada para assinar o token JWT

function array_to_object($array)
{
    return json_decode(json_encode($array), false);
}
 function token($headers, $token){
    
    $secretKey = "J1c2VyX2lkIjoiMjkiLCJ1c2VybmFtZ";
    $alg = "HS256"; 
    $headers = getallheaders();

    try {
        $headersObject = array_to_object($headers);
        // Decodificar o token JWT
        $tokenDecodificado = JWT::decode($token, new Key($secretKey, $alg));
    
        // Se o token JWT for válido, imprima o payload decodificado
        return $tokenDecodificado;
        
    } catch (Exception $e) {
        // Se houver algum erro ao decodificar o token, retorne um erro
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array("message" => $e->getMessage()));
        
    }
 }

