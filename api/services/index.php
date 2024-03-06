<?php

// Permitir solicitações de qualquer origem
header("Access-Control-Allow-Origin: *");

// Permitir métodos de solicitação GET, POST e OPTIONS
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Permitir os cabeçalhos de solicitação Authorization e Content-Type
header("Access-Control-Allow-Headers: Authorization, Content-Type");

// Permitir credenciais de usuário em solicitações
header("Access-Control-Allow-Credentials: true");

// Definir o tipo de conteúdo para aplicativo/json
header("Content-Type: application/json");

require_once("../../class/database.class.php");
require_once("../../function/validar-token.php");


$con = new Database();
$link = $con->getConexao();
$linkId = $con->getConexao();

$token = null;
$headers = getallheaders();

if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    http_response_code(401);
    exit(json_encode(array("message" => "Token de autorização não fornecido")));
}

$authorizationHeader = $_SERVER['HTTP_AUTHORIZATION'];
$tokenParts = explode(" ", $authorizationHeader);
$token = isset($tokenParts[1]) ? $tokenParts[1] : null;

if (!$token) {
    http_response_code(401);
    exit(json_encode(array("message" => "Token de autorização inválido")));
}

$validarToken = token($headers, $token);


if ($validarToken !== null) {
    // A decodificação foi bem-sucedida, agora você pode usar o objeto

    $id = $validarToken->user_id;

    if (isset($_GET['id'])) {

        $id_empresa = $_GET['id'];

        $query = 'SELECT catservico, MIN(id) AS id FROM servicos WHERE id_empresa = :id_empresa GROUP BY catservico;';
        $stmt = $link->prepare($query);
        $stmt->bindParam(':id_empresa', $id_empresa);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $serviceEmpresa = array(
                    'id' => $row['id'],
                    'catservico' => $row['catservico'],
                );
                $empresaArray[] = $serviceEmpresa;
            }

            header('Content-Type: application/json');
            echo json_encode($empresaArray);
        } else {
            header('Content-Type: application/json');
            echo json_encode(array("message" => "Empresa não encontrada."));
        }
    }

    if (isset($_GET['categoria']) && isset($_GET['id'])) {

        $categoria = $_GET['categoria'];
        $id_empresa = $_GET['id'];

        $query = 'SELECT * FROM servicos WHERE catservico = :categoria AND id_empresa = :id_empresa';
        $stmt = $link->prepare($query);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':categoria', $id_empresa);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $empresa = array(
                'id' => $row['id']
            );

            $empresaArray[] = $empresa;
            header('Content-Type: application/json');
            echo json_encode($empresaArray);
        } else {
            header('Content-Type: application/json');
            echo json_encode(array("message" => "Empresa não encontrada."));
        }



        echo $categoria;

    }

}