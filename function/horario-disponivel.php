<?php

function getHorariosDisponiveis($inicio, $fim, $inicioAlmoco, $fimAlmoco, $horariosMarcados) {
    $horarios = array();

    $inicio = strtotime($inicio);
    $fim = strtotime($fim);
    $inicioAlmoco = strtotime($inicioAlmoco);
    $fimAlmoco = strtotime($fimAlmoco);

    // Convertendo a hora de início para o próximo horário de 30 minutos
    $inicio = ceil($inicio / (30 * 60)) * (30 * 60);

    // Criando um array para armazenar os horários marcados
    $horariosOcupados = array();
    foreach ($horariosMarcados as $horarioMarcado) {
        $horarioInicio = strtotime($horarioMarcado['inicio']);
        $duracao = $horarioMarcado['duracao']; // Obtendo a duração do agendamento
        while ($duracao > 0) {
            $horariosOcupados[] = $horarioInicio;
            $horarioInicio += 30 * 60; // Adicionando 30 minutos em segundos
            $duracao -= 30; // Reduzindo a duração em 30 minutos
        }
    }

    $horarioAtual = $inicio;
    while ($horarioAtual <= $fim) {
        // Verifica se o horário atual não está dentro do horário de almoço e não está ocupado
        if (($horarioAtual < $inicioAlmoco || $horarioAtual >= $fimAlmoco) && !in_array($horarioAtual, $horariosOcupados)) {
            $horarios[] = date('H:i', $horarioAtual);
        }
        $horarioAtual += 30 * 60; // Adicionando 30 minutos em segundos
    }

    return $horarios;
}