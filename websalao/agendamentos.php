<style>
    section {
        flex-basis: 45%;
        height: 428px;
        border-radius: 5px;
        padding: 3px;
        background-color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .panel-heading {
        display: flex;
        justify-content: space-between;

    }

    .first {
        order: 1;
        /* Esta div ficará à esquerda */
        margin-left: 10px;
        margin-top: 8px;
        color: gray;
    }

    .second {
        order: 2;
        margin-right: 10px;
        margin-top: 8px;
        color: gray;
        /* Esta div ficará à direita */
    }

    h2 {
        margin-bottom: 10px;
        color: 'white';
    }

    .scroll-view {
        overflow-y: auto;
        /* Adiciona barra de rolagem vertical se necessário */
        max-height: 360px;
        /* Defina uma altura máxima para o ScrollView */

        /* Adicione uma borda para visualização */
        padding: 10px;
        /* Adicione preenchimento para o conteúdo */
        border-radius: 5px;
        background-color: whitesmoke;
    }

    /* Estilizando a barra de rolagem */
    .scroll-view::-webkit-scrollbar {
        width: 10px;
        /* largura da barra de rolagem */
    }

    .scroll-view::-webkit-scrollbar-track {
        background: #f1f1f1;
        /* cor de fundo da barra de rolagem */
    }

    .scroll-view::-webkit-scrollbar-thumb {
        background: #888;
        /* cor do "puxador" da barra de rolagem */
        border-radius: 5px;
        /* borda arredondada do "puxador" */
    }

    .scroll-view::-webkit-scrollbar-thumb:hover {
        background: #555;
        /* cor do "puxador" quando passa o mouse */
    }

    .container {
        display: flex;
        flex-wrap: wrap;
        width: '100%';
    }

    /* Estilos do card */
    .cardAgenda {
        width: 550px;
        /* Largura do card */
        height: 90px;
        /* Altura do card */
        border-radius: 10px;
        overflow: hidden;

        /* Cor da borda */
        margin-left: 10px;
        /* Espaçamento à esquerda */
        margin-bottom: 10px;
        /* Espaçamento abaixo */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        /* Sombra */
        display: flex;
        justify-content: space-between;
    }

    /* Estilos do lado esquerdo do card */
    .left-side {
        flex: 5;
        /*background: rgb(96, 96, 241);
        background: -moz-linear-gradient(0deg, rgba(96, 96, 241, 1) 0%, rgba(0, 188, 212, 1) 100%);
        background: -webkit-linear-gradient(0deg, rgba(96, 96, 241, 1) 0%, rgba(0, 188, 212, 1) 100%);
        background: linear-gradient(0deg, rgba(96, 96, 241, 1) 0%, rgba(0, 188, 212, 1) 100%);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#6060f1", endColorstr="#00bcd4", GradientType=1);
        */color: white;
        background-color: #00BCD4;
        padding: 5px;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }

    /* Estilos do lado direito do card */
    .right-side {
        flex: 1;
        background-color: white;
        /* Cor de fundo */
        color: #00BCD4;
        padding: 15px;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        /* Centraliza verticalmente */
        align-items: center;
        /* Centraliza horizontalmente */
    }

    /* Estilos para o texto grande */
    .big-text {
        font-size: 14px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .big-text-funcionario {
        font-size: 15px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .big-text-servico {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    /* Estilos para o texto pequeno */
    .small-text {
        font-size: 22px;
        font-weight: bold;
        color: #00BCD4;
    }

    .table-container {
        max-height: 360px;
        overflow-y: auto;
        border-radius: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;    
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #00BCD4;
        color:white;
        position: sticky; /* Fixa o cabeçalho */
        top: 0; /* Mantém o cabeçalho no topo */
        z-index: 2; /* Garante que o cabeçalho esteja acima do conteúdo da tabela */

    }
    /* Estilo para a barra de rolagem da tabela */
.table-container::-webkit-scrollbar {
    width: 10px; /* Largura da barra de rolagem */
}

/* Estilo para o trilho da barra de rolagem */
.table-container::-webkit-scrollbar-track {
    background: #f1f1f1; /* Cor de fundo do trilho */
}

/* Estilo para o "puxador" da barra de rolagem */
.table-container::-webkit-scrollbar-thumb {
    background: #888; /* Cor do "puxador" */
    border-radius: 5px; /* Bordas arredondadas do "puxador" */
}

/* Estilo para o "puxador" quando o mouse passa sobre ele */
.table-container::-webkit-scrollbar-thumb:hover {
    background: #555; /* Cor do "puxador" quando o mouse passa sobre ele */
}
</style>
<?php
require_once("../class/database.class.php");

$con = new Database();
$link = $con->getConexao();
$linkId = $con->getConexao();

$query = $query = "SELECT 
agenda.id AS id_agenda,
agenda.data_agendamento,
agenda.horario,
s.nome AS nome_servico,
f.nome AS nome_funcionario,
e.nome AS nome_empresa,
c.nome AS nome_cliente,
agenda.status
FROM 
agendamentos AS agenda
INNER JOIN 
servicos AS s ON agenda.id_servico = s.id
INNER JOIN 
funcionario AS f ON agenda.id_funcionario = f.id
INNER JOIN
empresas AS e ON f.id_empresa = e.id
INNER JOIN
users AS c ON agenda.id_cliente = c.id
WHERE 
agenda.id_empresa = 1
ORDER BY 
agenda.data_agendamento ASC,
agenda.horario ASC
LIMIT 100";
$stmt = $link->prepare($query);
$stmt2 = $link->prepare($query);
$stmt->execute();
$stmt2->execute();

$meses = array(
    "01" => "Janeiro",
    "02" => "Fevereiro",
    "03" => "Março",
    "04" => "Abril",
    "05" => "Maio",
    "06" => "Junho",
    "07" => "Julho",
    "08" => "Agosto",
    "09" => "Setembro",
    "10" => "Outubro",
    "11" => "Novembro",
    "12" => "Dezembro"
);

?>
<section>
    <!-- Aqui você pode adicionar informações sobre os agendamentos do dia -->
    <div class="panel">
        <div class="panel-heading">
            <div class="first">
                <h2>Agendamentos para hoje </h2>
            </div>
            <div class="second">
                <h2>
                    <?php
                    echo $stmt->rowCount()
                        ?>
                </h2>
            </div>
        </div>
        <div class="panel-body" style="height: 435px;">
            <section class="scroll-view">
                <!-- Aqui você pode adicionar o conteúdo que deseja que seja rolável -->
                <!-- Exemplo: -->
                <?php

                if ($stmt->rowCount() > 0) {

                    $data_atual = date("Y-m-d");

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $tempo = $row['horario'];
                        $tempoSemZeros = substr($tempo, 0, -3);
                        // Divide a data em ano, mês e dia
                        list($ano, $mes, $dia) = explode("-", $row['data_agendamento']);
                        // Obtem o nome do mês
                        $nome_mes = $meses[$mes];



                        if ($data_atual == $row['data_agendamento']) {
                            ?>
                            <div class="container">
                                <!-- Card de exemplo -->
                                <div class="cardAgenda">
                                    <!-- Lado esquerdo -->
                                    <div class="left-side">
                                        <div class="big-text">
                                            <h2>Cliente:
                                                <?php echo $row['nome_cliente'] ?>
                                            </h2>
                                        </div>
                                        <div class="big-text-funcionario">
                                            <?php echo $row['nome_funcionario'] ?>
                                        </div>
                                        <div class="big-text-servico">
                                            <h1>Serviço:
                                                <?php echo $row['nome_servico'] ?>
                                            </h1>
                                        </div>
                                    </div>
                                    <!-- Lado direito -->
                                    <div class="right-side">
                                        <div class="small-text">
                                            <?php echo $tempoSemZeros; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                        }

                    }
                }
                ?>
            </section>
        </div>

    </div>
</section>
<section>
    <div class="panel">
        <div class="panel-heading">
            <div class="first">
                <h2>Você possui <?php echo $stmt->rowCount()?> agendamentos</h2>
                
            </div>
            <div class="second">
                <h2>
                <input type="month" id="mesSelecionado" onchange="filtrarPorMes()">
                 <button onclick="filtrarPorData()">Filtrar</button>
                </h2>
            </div>
        </div>
        <div class="table-container">
            <table>
                <tr>
                    <th>Nome do Cliente</th>
                    <th>Nome do Executante</th>
                    <th>Nome do Serviço</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Editar</th>
                </tr>
                <?php

                $resultados = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                if ($stmt->rowCount() > 0) {

                    foreach ($resultados as $row1) {
                        $tempo = $row1['horario'];
                        $tempoSemZeros = substr($tempo, 0, -3);
                        // Divide a data em ano, mês e dia
                        list($ano, $mes, $dia) = explode("-", $row1['data_agendamento']);
                        // Obtem o nome do mês
                        $nome_mes = $meses[$mes];

                        echo "<tr>";
                        echo "<td>" . $row1['nome_cliente'] . "</td>";
                        echo "<td>" . $row1['nome_funcionario'] . "</td>";
                        echo "<td>" . $row1['nome_servico'] . "</td>";
                        echo "<td>" . $row1['data_agendamento'] . "</td>";
                        echo "<td>" . $tempoSemZeros . "</td>";
                        echo "<td><button>Editar</button></td>";                        
                        echo "</tr>";

                    }
                }
                ?>
            </table>
        </div>


    </div>
</section>