<?php

// Função para gerar horários disponíveis com intervalo de 30 minutos
function gerarHorariosDisponiveis($inicio, $fim, $duracao) {
    $horarios = array();
    $horarioAtual = $inicio;

    while ($horarioAtual <= $fim) {
        $horarios[] = $horarioAtual;
        $horarioAtual = strtotime('+' . $duracao . ' minutes', $horarioAtual);
    }
   
    return $horarios;
}

// Função para remover um serviço com duração específica dos horários disponíveis
function removerHorarioOcupado(&$horariosDisponiveis, $servicoDuracao) {
    $horariosOcupados = array();
    foreach ($horariosDisponiveis as $key => $horario) {
        $fimServico = strtotime('+' . $servicoDuracao . ' minutes', $horario);
        foreach ($horariosDisponiveis as $horarioDisponivel) {
            if ($horarioDisponivel >= $horario && $horarioDisponivel < $fimServico) {
                $horariosOcupados[] = $horariosDisponiveis[$key];
                echo date('H:i', $horarioDisponivel) . "\n";
                unset($horariosDisponiveis[$key]);
                break;
            }
        }
    }
  

    return $horariosOcupados;
}

$horariosDisponiveis = gerarHorariosDisponiveis(strtotime('08:00'), strtotime('18:00'), 30);

// Remover um serviço com duração de 30 minutos
$duracaoServico = 30;
$horariosRemovidos = removerHorarioOcupado($horariosDisponiveis, $duracaoServico);

// Imprimir os horários disponíveis restantes
echo "Horários disponíveis:\n";
foreach ($horariosDisponiveis as $horarioDisponivel) {
    echo date('H:i', $horarioDisponivel) . "\n";
}

?>