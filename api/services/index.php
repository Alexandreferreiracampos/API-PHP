<?php

require_once("../../class/database.class.php");

$con = new Database();
$link = $con->getConexao();

    $query = "SELECT * FROM services";
    $stmt = $link->prepare($query);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Inicializa um array para armazenar os resultados
        $services = array();

        // Loop através dos resultados e adiciona apenas os índices associativos ao array
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $service = array(
                'id' => $row['id'],
                'catSlug' => $row['catslug'],
                'name' => $row['service_name']
            );
            $services[] = $service;
        }

        // Retorna os dados como JSON
        header('Content-Type: application/json');
        echo json_encode($services);
   
} else {
    echo "Não existe dados de serviços";
}

$link = null; // Fechar conexão PDO

?>