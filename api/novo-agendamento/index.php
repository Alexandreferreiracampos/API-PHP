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

        $id_funcionario = $data->id_funcionario;
        $id_cliente = $id;
        $id_servico = $data->id_servico;
        $data_agendamento = date('Y-m-d', strtotime($data->data_agendamento));
        $horario = $data->horario;
        $duracao_servico = $data->duracao_servico;
        $id_empresa = $data->id_empresa;

        $queryVerificacao = "SELECT COUNT(*) AS total FROM agendamentos WHERE id_funcionario = :id_funcionario  AND data_agendamento = :data_agendamento AND horario = :horario";
        $stmtVerificacao = $link->prepare($queryVerificacao);
        $stmtVerificacao->bindParam(':id_funcionario', $id_funcionario);
        $stmtVerificacao->bindParam(':data_agendamento', $data_agendamento);
        $stmtVerificacao->bindParam(':horario', $horario);
        $stmtVerificacao->execute();
        $row = $stmtVerificacao->fetch(PDO::FETCH_ASSOC);

        if ($row['total'] > 0) {
            // Agendamento duplicado encontrado
            http_response_code(400);
            echo json_encode(array("message" => "Já existe um agendamento para esse horario."));
        } else {
            $query = "INSERT INTO agendamentos (id_funcionario, id_cliente, id_servico, data_agendamento, horario, duracao_servico, id_empresa) VALUES (:id_funcionario, :id_cliente, :id_servico, :data_agendamento, :horario, :duracao_servico, :id_empresa)";
            $stmt = $link->prepare($query);
    
            // Bind dos parâmetros
            $stmt->bindParam(':id_funcionario', $id_funcionario);
            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->bindParam(':id_servico', $id_servico);
            $stmt->bindParam(':data_agendamento', $data_agendamento);
            $stmt->bindParam(':horario', $horario);
            $stmt->bindParam(':duracao_servico', $duracao_servico);
            $stmt->bindParam(':id_empresa', $id_empresa);
    
            // Execução da query
            if ($stmt->execute()) {
                // Inserção bem-sucedida
                echo json_encode(array("ok" => "Agendamento criado com sucesso."));
            } else {
                // Erro na inserção
                http_response_code(500);
                echo json_encode(array("message" => "Erro ao criar agendamento."));
            }
        }

       


    }
}