<?php

require_once("../../class/database.class.php");
require_once("../../function/validar-token.php");


$con = new Database();
$link = $con->getConexao();
$linkId = $con->getConexao();

$token = null;
$headers = getallheaders();

if (isset($headers['Authorization'])) {
    $authorizationHeader = $headers['Authorization'];
    $tokenParts = explode(" ", $authorizationHeader);
    $token = isset($tokenParts[1]) ? $tokenParts[1] : null;
}

if (!$token) {
    http_response_code(401);
    exit("Token JWT não fornecido.");
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
                'nome_empresa' => $row['nome_empresa']
               
            );
            $agendamentos[] = $agendamento;
        }

        header('Content-Type: application/json');
        echo json_encode($agendamentos);

    }


}

