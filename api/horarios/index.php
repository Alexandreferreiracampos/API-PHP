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

require_once("../../function/validar-token.php");
require_once("../../function/horario-disponivel.php");
require_once("../../class/database.class.php");

$con = new Database();
$link = $con->getConexao();

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

    $id = $validarToken->user_id;

    if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER["CONTENT_TYPE"] === "application/json") {

        // Obtenha os dados do corpo da solicitação em JSON e decodifique-os
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data);
    
        $dataAgenda = $data->data;
        $id_funcionario = $data->id_funcionario;
    
        $query = 'SELECT inicio_expediente, fim_expediente, inicio_almoco, fim_almoco
                  FROM funcionario
                  WHERE id = :id_funcionario';
        $stmt = $link->prepare($query);
        $stmt->bindParam(':id_funcionario', $id_funcionario);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        $horarios = array(
            'inicio_expediente' => $row['inicio_expediente'],
            'fim_expediente' => $row['fim_expediente'],
            'inicio_almoco' => $row['inicio_almoco'],
            'fim_almoco' => $row['fim_almoco'],
        );
    
        $inicio = $horarios['inicio_expediente'];
        $fim = $horarios['fim_expediente'];
        $inicioAlmoco = $horarios['inicio_almoco'];
        $fimAlmoco = $horarios['fim_almoco'];
    
        $query = 'SELECT a.*
                  FROM funcionario AS f
                  LEFT JOIN agendamentos AS a ON f.id = a.id_funcionario
                  WHERE f.id = :id_funcionario AND DATE(a.data_agendamento) = :data_Agenda';
        $stmt = $link->prepare($query);
        $stmt->bindParam(':id_funcionario', $id_funcionario);
        $stmt->bindParam(':data_Agenda', $dataAgenda);
        $stmt->execute();
    
        $horariosMarcados = array();
        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $horariosMarcados[] = array(
                    'inicio' => $row['horario'],
                    'duracao' => $row['duracao_servico']
                );
            }
        }
    
        // Obtendo os horários disponíveis com base nos dados obtidos
        $horariosDisponiveis = getHorariosDisponiveis($inicio, $fim, $inicioAlmoco, $fimAlmoco, $horariosMarcados);
    
        header('Content-Type: application/json');
        echo json_encode($horariosDisponiveis);
    } else {



    }
}