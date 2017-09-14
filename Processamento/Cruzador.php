<?php

require_once '../Config.php';

use Apoio\Conexoes;
use Saida\K1;
use Saida\ControleJornada;
use Apoio\Erro;

\Apoio\Helpers::msg("Cruzador", "Buscando lista de veiculos do K1");
//Traz a lista de veiculos do k1 ------------
$k1 = new K1();
$veiculosK1 =  $k1->buscarVeiculos();
//Traz a lista de veiculos do k1 ------------

\Apoio\Helpers::msg("Cruzador", "Buscando lista de veiculos do CJ");
//Traz a lista de veiculos do cj ------------
$cj = new ControleJornada();
$veiculosCj =  $cj->buscarVeiculos();
//Traz a lista de veiculos do cj ------------

\Apoio\Helpers::msg("Cruzador", "Conectando com banco de dados");
$connCentral = Conexoes::conectarCentral();
while(true){
    
    \Apoio\Helpers::msg("Cruzador", "Buscando posicoes disponiveis");
    //Cria um array que vai lista os id's a serem apagados e busca as posições a serem verificadas----
    $listaApagar = array();
    $posicoes = $connCentral->query("select * from pacote_posicao limit 1000")->fetchAll();
    //Cria um array que vai lista os id's a serem apagados e busca as posições a serem verificadas----
    
    if(count($posicoes) > 0){
        foreach ($posicoes as $posicao){
            //junta o id e tecnologia para formar um id unico-------
            $id = strtolower($posicao['id_rastreador']);
            $tec = strtolower($posicao['tecnologia']);
            //junta o id e tecnologia para formar um id unico-------
            
            
            
            
            //Verificar se o veiculo existe na lista do k1-----------------
            if(in_array($id.$tec, $veiculosK1)){
                inserirPacotePosicaoSaida($posicao, "K1", $connCentral);
            }
            //Verificar se o veiculo existe na lista do k1-----------------
            
            //Verificar se o veiculo existe na lista do cj-----------------
            if(in_array($id.$tec, $veiculosCj)){
                inserirPacotePosicaoSaida($posicao, "CJ", $connCentral);
            }
            //Verificar se o veiculo existe na lista do cj-----------------
            
            
            
            
            //Criar um array com os id's para apagar depois de processar------
            array_push($listaApagar, $posicao['id']);
            //Criar um array com os id's para apagar depois de processar------
        }
        \Apoio\Helpers::msg("Cruzador", count($posicoes)." posicoes cruzadas");
        \Apoio\Helpers::msg("Cruzador", "Deletando posicoes processadas");
        $connCentral->query("delete from pacote_posicao where pacote_posicao.id in (".implode(",", $listaApagar).")");
    }else{
        \Apoio\Helpers::msg("Cruzador", "Nenhuma nova posicao encontrada");
        break;
    }
}


function inserirPacotePosicaoSaida($posicao, $programa, $connCentral){
    $sql = " insert into pacote_posicao_saida set "
            . "id_seq_mensagem = '{$posicao['id_seq_mensagem']}', "
            . "data_hora_evento = '{$posicao['data_hora_evento']}', "
            . "data_hora_pacote = '{$posicao['data_hora_pacote']}', "
            . "id_rastreador = '{$posicao['id_rastreador']}', "
            . "placa = '{$posicao['placa']}', "
            . "id_motorista = '{$posicao['id_motorista']}', "
            . "nome_motorista = '{$posicao['nome_motorista']}', "
            . "latitude = '{$posicao['latitude']}', "
            . "longitude = '{$posicao['longitude']}', "
            . "direcao = '{$posicao['direcao']}', "
            . "ignicao = '{$posicao['ignicao']}', "
            . "id_evento = '{$posicao['id_evento']}', "
            . "evento = '{$posicao['evento']}', "
            . "id_macro = '{$posicao['id_macro']}', "
            . "mensagem_macro = '{$posicao['mensagem_macro']}', "
            . "id_mensagem_livre = '{$posicao['id_mensagem_livre']}', "
            . "mensagem_livre = '{$posicao['mensagem_livre']}', "
            . "velocidade = '{$posicao['velocidade']}', "
            . "odometro = '{$posicao['odometro']}', "
            . "tecnologia = '{$posicao['tecnologia']}', "
            . "localizacao = '{$posicao['localizacao']}',"
            . "tensao_bateria = '{$posicao['tensao_bateria']}', "
            . "programa = '{$programa}'";
            
    try{
        $connCentral->query($sql); 
    } catch (Exception $ex) {
        $erro = new Erro("Cruzador", "inserirPacotePosicaoSaida", "Erro ao executar query em banco de dados", date("Y-m-d H:i:s"));
        $erro->registrarErro();
    }
           
}