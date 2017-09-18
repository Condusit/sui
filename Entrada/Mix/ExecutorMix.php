<?php

//Requisita classe para autoload das classes---
require_once '../../Config.php';
//Requisita classe para autoload das classes---


//Carrega os uses necessário para execução das classes----
use Entrada\Mix\Veiculo;
use Entrada\Mix\Evento;
use Entrada\Mix\Motorista;
use Entrada\Mix\Posicao;
use Entrada\Mix\EventoNotificacao;
use Entrada\Mix\Construtor;
use Apoio\Conexoes;
//Carrega os uses necessário para execução das classes----

//Executa o fluxo do programa-------------------------------------
\Apoio\Helpers::msg("Integracao Mix", "Iniciando ciclo");

$connMix = Conexoes::conectarMix();
$dataSecundaria = $connMix->query("select * from controle where controle.nome = 'secundarios'")->fetchAll();
$dataComMais2Horas = date("Y-m-d H:i:s", strtotime("+2 hours", strtotime($dataSecundaria[0]['id'])));
$data = date("Y-m-d H:i:s");
if($data > $dataComMais2Horas){
    $vei = new Veiculo();
    $vei->atualizarListaVeiculos();
    $eve = new Evento();
    $eve->atualizarListaEventos();
    $mot = new Motorista();
    $mot->atualizarListaMotorista();
    $connMix->query("update controle set controle.id = '{$data}' where controle.nome = 'secundarios'");
}


$pos = new Posicao();
$pos->buscarPosicoes();
$eveN = new EventoNotificacao();
$eveN->buscarEventoNotificacao();
$con = new Construtor();
$con->gerarPosicoes();

\Apoio\Helpers::msg("Integracao Mix", "Ciclo terminado");
//Executa o fluxo do programa-------------------------------------