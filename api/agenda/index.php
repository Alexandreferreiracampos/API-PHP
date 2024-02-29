<?php

require_once("../../class/database.class.php");

$con = new Database();
$link = $con->getConexao();
$linkId = $con->getConexao();

$horariosAll = ["07:00", "08:00", "09:00", "10:00", "11:00", "12:00","13:00", "14:00", "15:00", "16:00", "17:00", "18:00"];

if(isset($_GET['servico']) && isset($_GET['dateagenda'])){

    $servico = $_GET['servico'];
    $dateagenda = $_GET['dateagenda'];

    $query = "SELECT * FROM agenda WHERE catslug = :servico AND date = :dateagenda";
    $stmt = $link->prepare($query);
    $stmt->bindParam(':servico', $servico);
    $stmt->bindParam(':dateagenda', $dateagenda);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
       
        $servico = array();
       
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $hora_a_remover = json_decode($row['agendadas'], true);
            $horarios = array_diff($horariosAll, $hora_a_remover);
            $horarios = array_values($horarios);

            $service = array(
                'id' => $row['id'],
                'catSlug' => $row['catslug'],
                'date' => $row['date'],
                'agendadas' => $horarios
            );
            $servico[] = $service;
        }

        header('Content-Type: application/json');
        echo json_encode($servico);

    }else {

        $query = "SELECT * FROM agenda WHERE catslug = :servico";
        $queryId = "SELECT MAX(id) FROM agenda";
        $stmt = $link->prepare($query);
        $stmtId = $linkId->prepare($queryId);
        $stmt->bindParam(':servico', $servico);
        $stmt->execute();
        $stmtId->execute();
        
        if ($stmt->rowCount() > 0) {

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $rowId = $stmtId->fetch(PDO::FETCH_ASSOC);
            $next_id = $rowId['MAX(id)'] + 1;

            $service = array(
                'id' => $next_id,
                'catSlug' => $servico,
                'date' => $dateagenda,
                'agendadas' => $horariosAll
            );

            header('Content-Type: application/json');
            echo json_encode([$service]);            

        }else{

            $service = array(
                
                'catSlug' => $servico,
                'date' => $dateagenda,
                'agendadas' => $horariosAll
            );
            header('Content-Type: application/json');
            echo json_encode([$service]);  
           
        }

    }

} else if (isset($_GET['servico'])) {
    
    $servico = $_GET['servico'];

    $query = "SELECT * FROM agenda WHERE catslug = :servico";
    $stmt = $link->prepare($query);
    $stmt->bindParam(':servico', $servico);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
       
        $servico = array();

      
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $service = array(
                'id' => $row['id'],
                'catSlug' => $row['catslug'],
                'date' => $row['date'],
                'agendadas' => json_decode($row['agendadas'])
            );
            $servico[] = $service;
        }

        header('Content-Type: application/json');
        echo json_encode($servico);

    }else {
        echo "Não foram encontrados registros na tabela agenda.";
    }
    
}else {

    $query = "SELECT * FROM agenda";
    $stmt = $link->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Inicializa um array para armazenar os resultados
        $agendas = array();

        // Loop através dos resultados e adiciona apenas os índices associativos ao array
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $agenda = array(
                'id' => $row['id'],
                'catSlug' => $row['catslug'],
                'date' => $row['date'],
                'agendadas' => json_decode($row['agendadas'])
            );
            $agendas[] = $agenda;
            
        }

        // Retorna os dados como JSON
        header('Content-Type: application/json');
        echo json_encode($agendas);
    }
}

$link = null; // Fechar conexão PDO

?>