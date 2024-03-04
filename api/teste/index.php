if (isset($headers['Authorization'])) {
        $authorizationHeader = $headers['Authorization'];
        $tokenParts = explode(" ", $authorizationHeader);
        $token = isset($tokenParts[1]) ? $tokenParts[1] : null;
    }

    if (!$token) {
        http_response_code(401);
        exit("Token JWT não fornecido.");
    }

    // Chave secreta usada para assinar o token JWT
    $secretKey = "J1c2VyX2lkIjoiMjkiLCJ1c2VybmFtZ";
    $alg = "HS256";

    try {
        $headersObject = array_to_object($headers);
        // Decodificar o token JWT
        $tokenDecodificado = JWT::decode($token, new Key($secretKey, $alg));

        // Se o token JWT for válido, imprima o payload decodificado
        print_r($tokenDecodificado);
    } catch (Exception $e) {
        // Se houver algum erro ao decodificar o token, retorne um erro
        http_response_code(401);
        exit("Erro ao decodificar o token JWT: " . $e->getMessage());
    }