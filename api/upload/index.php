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

    $id = $validarToken->user_id;

    // Verifique se o arquivo foi enviado sem erros
    if ($_FILES['imagem']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        exit(json_encode(array("message" => "Erro ao enviar o arquivo")));
    }

    // Verifica o tipo MIME do arquivo
    $imageFileType = strtolower(pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION));
    $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
    if (!in_array($imageFileType, $allowedTypes)) {
        http_response_code(400);
        exit(json_encode(array("message" => "Apenas arquivos de imagem são permitidos (JPG, JPEG, PNG, GIF)")));
    }

    // Verifica o tipo de imagem usando exif_imagetype
    $imageType = exif_imagetype($_FILES["imagem"]["tmp_name"]);
    if (!$imageType || !in_array($imageType, array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF))) {
        http_response_code(400);
        exit(json_encode(array("message" => "Arquivo enviado não é uma imagem válida")));
    }

    // Diretório de destino para salvar as imagens
    $targetDir = "../../imagens/";

    // Crie um nome único para o arquivo
    $hash = md5(uniqid(rand(), true));
    $extensao = pathinfo($_FILES["imagem"]["name"], PATHINFO_EXTENSION);
    $nomeAleatorio = $hash . '.' . $extensao;
    $targetFile = $targetDir . $nomeAleatorio;

    // Verifique se o arquivo já existe
    if (file_exists($targetFile)) {
        http_response_code(400);
        exit(json_encode(array("message" => "O arquivo já existe")));
    }

    // Mova o arquivo temporário para o diretório de destino
    if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $targetFile)) {
        echo json_encode(array("message" => "Arquivo enviado com sucesso"));

        $query = "SELECT * FROM empresas WHERE id = :idEmpresa";
        $stmt = $link->prepare($query);
        $stmt->bindParam(':idEmpresa', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Recuperar o nome do arquivo do banco de dados
            $nome_arquivo = $row['img'];

            if(!$nome_arquivo){
                    $query = "UPDATE empresas set img = :nameIMG WHERE id = :IDusuario";
                    $stmt = $link->prepare($query);
                    $stmt->bindParam(':IDusuario', $id);
                    $stmt->bindParam(':nameIMG', $nomeAleatorio);
                    $stmt->execute();
            }else{
                // Diretório onde as fotos estão armazenadas
            $diretorio = $targetDir;

            // Caminho completo para o arquivo
            $caminho_arquivo = $diretorio . $nome_arquivo;

            // Verificar se o arquivo existe antes de excluí-lo
            if (file_exists($caminho_arquivo)) {
                // Tentar excluir o arquivo
                if (unlink($caminho_arquivo)) {
                    echo "Arquivo excluído com sucesso.";

                    $query = "UPDATE empresas set img = :nameIMG WHERE id = :IDusuario";
                    $stmt = $link->prepare($query);
                    $stmt->bindParam(':IDusuario', $id);
                    $stmt->bindParam(':nameIMG', $nomeAleatorio);
                    $stmt->execute();

                } else {
                    echo "Erro ao excluir o arquivo.";
                }
            } else {
                echo "O arquivo não existe.";

                $query = "UPDATE empresas set img = :nameIMG WHERE id = :IDusuario";
                    $stmt = $link->prepare($query);
                    $stmt->bindParam(':IDusuario', $id);
                    $stmt->bindParam(':nameIMG', $nomeAleatorio);
                    $stmt->execute();

            }
            }
            
            
           
        }

    } else {
        // Se houver algum erro ao mover o arquivo, retorne um erro
        http_response_code(500);
        echo json_encode(array("message" => "Erro ao enviar o arquivo"));
    }
}