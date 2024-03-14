<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .login-container input[type="tel"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .login-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #00BCD2;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-container input[type="submit"]:hover {
            background-color: #00cdd2;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
    </style>

    <?php
    $error = isset($_GET['error']) ? $_GET['error'] : null;

    // Função para exibir mensagens de erro
    function displayErrorMessage($error)
    {
        switch ($error) {
            case 'invalid_credentials':
                return "Credenciais inválidas. Por favor, tente novamente.";
            case 'user_not_found':
                return "Usuário não encontrado. Por favor, verifique seu telefone e senha.";
            default:
                return "";
        }
    }

    if (isset($_COOKIE['token']) && !empty($_COOKIE['token'])) {
        // Redireciona para a página principal
        header("Location: pagina_principal.php");
        exit(); // Certifique-se de sair após o redirecionamento
    } else {

    }
    ?>

</head>

<body>
    <div class="login-container">
        <h2>Mega Salão</h2>
        <form action="validar_login.php" method="post">
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo displayErrorMessage($error); ?>
                </div>
            <?php endif; ?>
            <input type="tel" id="telefone" name="telefone" placeholder="Telefone" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <input type="submit" value="Login">
        </form>
    </div>
    <script>
        const tel = document.getElementById('telefone');

        tel.addEventListener('input', (e) => mascaraTelefone(e.target.value));

        const mascaraTelefone = (valor) => {
            // Remove caracteres não numéricos
            valor = valor.replace(/\D/g, "");

            // Formatação para (XX) XXXX-XXXX
            if (valor.length <= 11) {
                valor = valor.replace(/^(\d{2})(\d)/g, "($1) $2");
                valor = valor.replace(/(\d)(\d{4})$/, "$1-$2");
            }
            // Formatação para (XX) XXXXX-XXXX
            else {
                valor = valor.replace(/^(\d{2})(\d)/g, "($1) $2");
                valor = valor.replace(/(\d)(\d{5})$/, "$1-$2");
            }

            // Atualiza o valor no campo de telefone
            tel.value = valor.slice(0, 15);

        };
    </script>
</body>

</html>