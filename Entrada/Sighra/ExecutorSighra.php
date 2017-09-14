<?php
require_once '../../Config.php';

use Entrada\Sighra\Comando;
use Entrada\Sighra\Evento;
use Entrada\Sighra\Macro;
use Entrada\Sighra\Motorista;
use Entrada\Sighra\Ponto;
use Entrada\Sighra\Veiculo;
use Entrada\Sighra\Mensagem;

$comando = new Comando();
$comando->buscarListaComandos();

$eventos = new Evento();
$eventos->buscarListaEventos();

$macro = new Macro();
$macro->buscarListaMacros();
$macro->buscarMacros();

$motorista = new Motorista();
$motorista->buscarListaMotoristas();

$ponto = new Ponto();
$ponto->buscarListaPontos();

$veiculo = new Veiculo();
$veiculo->buscarListaVeiculos();

$mensagem = new Mensagem();
$mensagem->buscarMensagens();