<?php

require_once '../Config.php';

use Processamento\Mix;
use Processamento\Tracker;
use Processamento\Sighra;



$mix = new Mix();
$mix->buscarPosicoes();


$tracker = new Tracker();
$tracker->buscarPosicoes();

$sighra = new Sighra();
$sighra->buscarPosicoes();