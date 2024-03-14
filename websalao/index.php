
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Controle - Salão de Beleza</title>
     <!-- <link rel="stylesheet" href="styles.css">-->
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f0f0f0;
   
}

header, nav, main, footer {
    padding: 20px;
}

header {
    background-color: #00BCD2;
    color: #fff;
    text-align: center;
    height: 20px; /* Define a altura do cabeçalho como 50 pixels */
    line-height: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

nav ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

nav ul li {
    display: inline;
    margin-right: 20px;
    
}

nav ul li a {
    text-decoration: none;
    color: #333;
    font-weight: bold;
}

nav ul li a:hover {
    color: #666;
}

main {
    display: flex;
    justify-content: space-around;
    
}

section {
    flex-basis: 45%;
}

footer {
    background-color: #00BCD2;
    color: #fff;
    text-align: center;
    position: fixed;
    bottom: 0;
    width: 100%;
    height: 10px;
    line-height: 10px; /* Centraliza o texto verticalmente */
}

/* Estilos específicos */
h1, h2 {
    margin: 0;
}

h2 {
    margin-bottom: 10px;
    color:'white';
}

ul {
    padding: 0;
}

li {
    list-style: none;
    margin-bottom: 10px;
}

nav ul {
    list-style-type: none;
    margin: 0;
    padding: 0;
 
}

nav ul li {
    display: inline;
    margin-right: 10px;
}

nav ul li a {
    color: gray; /* Cor do texto normal */
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
}

/* Adiciona a cor de fundo azul quando o link estiver selecionado */
nav ul li a.selected {
    background-color: #00BCD2;
    color: #fff; /* Cor do texto quando selecionado */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

main {
    padding: 20px;
    
}
</style>
<?php

require_once("../class/database.class.php");

$con = new Database();
$link = $con->getConexao();
$linkId = $con->getConexao();

$query = "SELECT * FROM empresas";
$stmt = $link->prepare($query);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

 $nomeSalao = $row["nome"];

?>
<body onload="loadPage('agendamentos.php', false)">

    <header>
        <h1>Painel de Controle - <?php echo $nomeSalao ?></h1>
    </header>
    <nav>
        <ul>
            <li><a href="#" onclick="loadPage('agendamentos.php', true)" class="selected">Agendamentos</a></li>
            <li><a href="#" onclick="loadPage('funcionarios.html', true)">Funcionários</a></li>
            <li><a href="#" onclick="loadPage('servicos.html', true)">Serviços</a></li>
            <li><a href="#" onclick="loadPage('clientes.html', true)">Clientes</a></li>
        </ul>
    </nav>
    
    <main id="content"></main>

    <script>
        function loadPage(page, select) {
            // Remove a classe "selected" de todos os links
            var links = document.querySelectorAll('nav a');
            links.forEach(function(link) {
                link.classList.remove('selected');
            });
            
            if(select){
               // Adiciona a classe "selected" ao link clicado
               event.target.classList.add('selected');
            }else{
                links[0].classList.add('selected');
            }
            
            // Carrega o conteúdo da página
            fetch(page)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('content').innerHTML = html;
                });
        }

        function solicitarPermissao() {
            // Verifica se o navegador suporta notificações
            if (!("Notification" in window)) {
                alert("Este navegador não suporta notificações.");
            } else {
                // Verifica se as notificações estão permitidas, senão solicita permissão
                if (Notification.permission === "granted") {
                    mostrarNotificacao();
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(function (permission) {
                        if (permission === "granted") {
                            mostrarNotificacao();
                        }
                    });
                }
            }
        }

        function mostrarNotificacao() {
            var options = {
                body: "Esta é uma notificação de exemplo.",
                icon: "https://example.com/notification-icon.png"
            };
            var notification = new Notification("Título da Notificação", options);

            // Você pode adicionar um evento de clique para a notificação se desejar
            notification.onclick = function() {
                window.focus();
                notification.close();
                // Adicione aqui o redirecionamento ou a ação desejada ao clicar na notificação
            };
        }
    </script>
</body>
</html>
