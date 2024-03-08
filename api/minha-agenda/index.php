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
    agenda.data_agendamento,
    agenda.horario,
    s.nome AS nome_servico,
    f.nome AS nome_funcionario,
    e.nome AS nome_empresa,
    agenda.status
FROM 
    agendamentos AS agenda
INNER JOIN 
    servicos AS s ON agenda.id_servico = s.id
INNER JOIN 
    funcionario AS f ON agenda.id_funcionario = f.id
INNER JOIN
    empresas AS e ON f.id_empresa = e.id
WHERE 
    agenda.id_cliente = :usuario 
ORDER BY 
    agenda.data_agendamento ASC,
    agenda.horario ASC
LIMIT 100";
    $stmt = $link->prepare($query);
    $stmt->bindParam(':usuario', $id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {

        $agendamento = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tempo = $row['horario'];
            $tempoSemZeros = substr($tempo, 0, -3); 
            $agendamento = array(
                'id_agenda' => $row['id_agenda'],
                'nome_funcionario' => $row['nome_funcionario'],
                'nome_servico' => $row['nome_servico'],
                'nome_empresa' => $row['nome_empresa'],
                'horario'=> $tempoSemZeros,
                'data'=> $row['data_agendamento'],
                'status' => $row['status']
            );
            $agendamentos[] = $agendamento;
        }

        header('Content-Type: application/json');
        echo json_encode($agendamentos);

    }


}

