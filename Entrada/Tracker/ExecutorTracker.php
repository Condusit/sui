<?php

require_once '../../Config.php';

use Entrada\Tracker\Veiculo;
use Entrada\Tracker\Construtor;


$veiculo = new Veiculo();
$veiculo->buscarVeiculos();

$construtor = new Construtor();
$construtor->gerarPosicao();