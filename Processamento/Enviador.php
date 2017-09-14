<?php

require_once '../Config.php';

use Apoio\Conexoes;
use Saida\K1;
use Saida\ControleJornada;

$connCentral = Conexoes::conectarCentral();
$k1 = new K1();
$cj = new ControleJornada();

while(true){
    $posicoes = $connCentral->query("select pps.*, '0' as numero_viagem from pacote_posicao_saida pps limit 1000")->fetchAll();
    if(count($posicoes) > 0){
        $listaK1 = array();
        $listaCj = array();
        $deletar = array();
        foreach ($posicoes as $posicao){
            switch ($posicao['programa']){
                case "K1":
                    array_push($listaK1, $posicao);
                    array_push($deletar, $posicao['id']);
//                    $k1->enviaPosicao($posicao[0]);
//                    $connCentral->query("delete from pacote_posicao_saida where pacote_posicao_saida.id = '{$posicao[0]['id']}'");
                    break;
                case "CJ":
                    array_push($listaCj, $posicao);
                    array_push($deletar, $posicao['id']);
//                    $cj->enviaPosicao($posicao[0]);
//                    $connCentral->query("delete from pacote_posicao_saida where pacote_posicao_saida.id = '{$posicao[0]['id']}'");
                    break;
            }
        }
        if(count($listaK1) > 0){
            $k1->enviaPosicao($listaK1);
        }
        if(count($listaCj) > 0){
            $cj->enviaPosicao($listaCj);
        }
        $deletarStr = "(".implode(",", $deletar).")";
        $connCentral->query("delete from pacote_posicao_saida where pacote_posicao_saida.id in {$deletarStr}");
    }else{
        \Apoio\Helpers::msg("Enviador", "Nenhuma posicao para envio encontrada");
        break;
    }
}

