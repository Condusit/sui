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
//Carrega os uses necessário para execução das classes----

//Executa o fluxo do programa-------------------------------------
\Apoio\Helpers::msg("Integracao Mix", "Iniciando ciclo");

$vei = new Veiculo();
$vei->atualizarListaVeiculos();
$eve = new Evento();
$eve->atualizarListaEventos();
$mot = new Motorista();
$mot->atualizarListaMotorista();
$pos = new Posicao();
$pos->buscarPosicoes();
$eveN = new EventoNotificacao();
$eveN->buscarEventoNotificacao();
$con = new Construtor();
$con->gerarPosicoes();

\Apoio\Helpers::msg("Integracao Mix", "Ciclo terminado");
//Executa o fluxo do programa-------------------------------------