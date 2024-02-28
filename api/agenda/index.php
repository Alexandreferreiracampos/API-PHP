<?php

require_once("../../class/database.class.php");

$con = new Database();
$link = $con->getConexao();

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
            $service = array(
                'id' => $row['id'],
                'catSlug' => $row['catslug'],
                'date' => $row['date'],
                'agendadas' => $row['agendadas']
            );
            $servico[] = $service;
        }

        header('Content-Type: application/json');
        echo json_encode($servico);

    }else {
        echo "Não foram encontrados registros na tabela agenda.";
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
                'agendadas' => $row['agendadas']
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
                'agendadas' => $row['agendadas']
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