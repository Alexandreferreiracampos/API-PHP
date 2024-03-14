<?php
require_once("../vendor/autoload.php"); // Inclua a biblioteca JWT (se você estiver usando composer)
require_once("../class/database.class.php");

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

// Verifica se os dados foram enviados por POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtém os dados do formulário
    $telefone = $_POST["telefone"];
    $senha = $_POST["senha"];

    $telefoneSemMascara = preg_replace('/\D/', '', $telefone);

    $query = "SELECT * FROM users WHERE telefone = :telefone";
    $stmt = $link->prepare($query);
    $stmt->bindParam(':telefone', $telefoneSemMascara);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) {

        if (password_verify($senha, $row['senha'])) {

            $token = generateJWT($row['id'], $row['nome']);
            echo $token;
        }else{
            header("Location: login.php?error=invalid_credentials");
        }
    }else{
        header("Location: login.php?error=user_not_found");
        exit(); // Certifique-se de sair após o redirecionamento
    }
}