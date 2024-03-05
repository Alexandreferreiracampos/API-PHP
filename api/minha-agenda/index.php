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

    $query = "SELECT 
    agenda.id AS id_agenda,
    agenda.service_name,
    agenda.catSlug,
    agenda.mes,
    agenda.dia,
    agenda.hora,
    agenda.date,
    agenda.status,
    s.id AS id_servico,
    s.nome AS nome_servico,
    e.id AS id_empresas,
    e.nome AS nome_empresa
    FROM 
    minha_agenda agenda
    INNER JOIN 
    servicos s ON agenda.id_servico = s.id
    INNER JOIN 
    empresas e ON agenda.id_empresa = e.id
    WHERE 
    agenda.id_usuario = :usuario LIMIT 100";
    $stmt = $link->prepare($query);
    $stmt->bindParam(':usuario', $id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        $agendamento = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $agendamento = array(
                'id' => $row['id_agenda'],
                'service_name' => $row['service_name'],
                'catSlug' => $row['catSlug'],
                'mes' => $row['mes'],
                'dia' => $row['dia'],
                'date' => $row['date'],
                'hora' => $row['hora'],
                'nome_servico' => $row['nome_servico'],
                'id_servico' => $row['id_servico'],
                'id_empresas' => $row['id_empresas'],
                'nome_empresa' => $row['nome_empresa'],
                'status'=> $row['status']
               
            );
            $agendamentos[] = $agendamento;
        }

        header('Content-Type: application/json');
        echo json_encode($agendamentos);

    }


}

