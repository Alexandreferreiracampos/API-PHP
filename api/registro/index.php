<?php
require_once("../../vendor/autoload.php"); // Inclua a biblioteca JWT (se você estiver usando composer)
require_once("../../class/database.class.php");

use Firebase\JWT\JWT;

$con = new Database();
$link = $con->getConexao();

// Função para gerar um token JWT
function generateJWT($userId, $username)
{
    $payload = array(
        "user_id" => $userId,
        "username" => $username,
        "exp" => time() + (60 * 60) // Token expira em 1 hora
    );
    $key = "your_secret_key"; // Chave secreta
    $alg = "HS256"; // Algoritmo de assinatura JWT
    return JWT::encode($payload, $key, $alg); // Substitua "your_secret_key" pela sua chave secreta real
}

// Verifique se os dados do usuário foram enviados via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER["CONTENT_TYPE"] === "application/json") {

    // Obtenha os dados do corpo da solicitação em JSON e decodifique-os
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data);

    // Verifique se os dados esperados estão presentes
    if (isset($data->username) && isset($data->password) && isset($data->phone)) {
        // Obtenha os dados do usuário
        $username = $data->username;
        $password = $data->password;
        $phone = $data->phone;

        // Hash da senha (você pode usar algoritmos mais seguros, como bcrypt)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $link->prepare("SELECT COUNT(*) FROM users WHERE telefone = :phone");
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo "Usuario ja existente";
        } else {
             // Insira os dados do usuário no banco de dados
        $stmt = $link->prepare("INSERT INTO users (nome, senha, telefone) VALUES (?, ?, ?)");
        $stmt->bindValue(1, $username, PDO::PARAM_STR);
        $stmt->bindValue(2, $hashedPassword, PDO::PARAM_STR);
        $stmt->bindValue(3, $phone, PDO::PARAM_STR);
        $stmt->execute();

        // Obtenha o ID do usuário recém-criado
        $userId = $link->lastInsertId();

        // Gere um token JWT para o usuário
        $token = generateJWT($userId, $username);

        // Retorne os dados do usuário junto com o token JWT
        $response = array(
            "token" => $token,
            "user" => array(
                "user_id" => $userId,
                "username" => $username
            )
        );

        header("Content-Type: application/json");
        echo json_encode($response);
        }

    } else {

        // Se os dados esperados não estiverem presentes, retorne um erro
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(array("message" => "Dados de usuário incompletos."));
    }
} else {

    // Se a solicitação não for POST ou não for JSON, retorne um erro
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(array("message" => "Apenas solicitações POST JSON são suportadas."));
}

$link = null; // Fechar conexão PDO
?>