<?php

require_once("../../vendor/autoload.php"); // Inclua a biblioteca JWT (se você estiver usando composer)
require_once("../../class/database.class.php");

$con = new Database();
$link = $con->getConexao();
$linkId = $con->getConexao();

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function array_to_object($array)
{
    return json_decode(json_encode($array), false);
}

function generateJWT($userId, $username)
{
    $payload = array(
        "user_id" => $userId,
        "username" => $username,
        "exp" => time() + (60 * 60) // Token expira em 1 hora
    );
    $key = "J1c2VyX2lkIjoiMjkiLCJ1c2VybmFtZ"; // Chave secreta
    $alg = "HS256"; // Algoritmo de assinatura JWT
    return JWT::encode($payload, $key, $alg); // Substitua "your_secret_key" pela sua chave secreta real
}

// Obtenha o token JWT do cabeçalho Authorization
$token = null;
$headers = getallheaders();

if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER["CONTENT_TYPE"] === "application/json") {

    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data);

    if (isset($data->phone) && isset($data->password)) {

        $query = "SELECT * FROM users WHERE telefone = :telefone";
        $stmt = $link->prepare($query);
        $stmt->bindParam(':telefone', $data->phone);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() > 0) {

            if (password_verify($data->password, $row['senha'])) {

                $token = generateJWT($row['id'], $row['nome']);

                // Retorne os dados do usuário junto com o token JWT
                $response = array(
                   "token" => $token,
                   "user" => array(
                   "user_id" => $row['id'],
                   "username" => $row['nome']
                ));
                
                 header("Content-Type: application/json");
                 echo json_encode($response);
               
        } else {
           
            header("Content-Type: application/json");
            echo json_encode(array("message"=>"Senha incorreta!"));
        }

        } else {
            
            header("Content-Type: application/json");
            echo json_encode(array("message"=>"Telefone não cadastrado!"));
        }



    } else {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array("message" => "Não foram fornecidos os dados corretos."));
    }


}