<?php

require_once '../Config.php';

use Processamento\Mix;
use Processamento\Tracker;


//Busca posicões da Mix---
$mix = new Mix();
$mix->buscarPosicoes();
//Busca posicões da Mix---

$tracker = new Tracker();
$tracker->buscarPosicoes();