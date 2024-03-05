<?php

require_once("../../class/database.class.php");

$con = new Database();
$link = $con->getConexao();
$linkId = $con->getConexao();

if(isset($_GET['phone'])){

    $phone_empresa = $_GET['phone'];

    $query = 'SELECT * FROM empresas WHERE telefone = :phone';
    $stmt = $link->prepare($query);
    $stmt -> bindParam(':phone', $phone_empresa);
    $stmt -> execute();

    if($stmt->rowCount() > 0){

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
      
        $empresa = array(
            'id'=> $row['id'],
            'nome'=> $row['nome'],
            'telefone'=> $row['telefone'],
            'img'=> $row['img'],
            'dia_semana'=> $row['dia_semana'],
            'fim_semana'=> $row['fim_semana'],
        );

        $empresaArray[] = $empresa;
        header('Content-Type: application/json');
        echo json_encode($empresaArray);
    }else{
        header('Content-Type: application/json');
        echo json_encode(array("message" => "Empresa não encontrada."));
    }
   

}
