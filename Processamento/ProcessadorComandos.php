<?php
namespace Processamento;
require_once '../Config.php';

use Saida\K1;
use Entrada\Sighra\Comando;
use Apoio\Conexoes;



$k1 = new K1();
$sighra = new Comando();
$connCentral = Conexoes::conectarCentral();



//Busca o comando nas tecnologias que utilizam o mesmo-----------------------
$k1->buscarComandos();
//Busca o comando nas tecnologias que utilizam o mesmo-----------------------


//Envia comandos para suas respectivas tecnologias---------------------------------
enviaComandos("sighra");
//Envia comandos para suas respectivas tecnologias---------------------------------


//Atualiza comandos para suas respectivas tecnologias---------------------------------
atualizaComandos("sighra");
//Atualiza comandos para suas respectivas tecnologias---------------------------------


//Atualiza o comando nas tecnologias que utilizam o mesmo-----------------------
$k1->atualizaComandos();
//Atualiza o comando nas tecnologias que utilizam o mesmo-----------------------



function enviaComandos($tecnologia){
    $connCentral = Conexoes::conectarCentral();
    switch ($tecnologia){
        case "sighra":
            $enviador = new \Entrada\Sighra\Comando;
            break;
        default :
            \Apoio\Helpers::msg("Comandos", "A tecnologia {$tecnologia} ainda não possui suporte no software SUI");
    }
    
    
    $comandos = $connCentral->query("select * from comandos com where com.tecnologia = '{$tecnologia}' and com.`status` = 'A Enviar'")->fetchAll();
    
    if(count($comandos) > 0){
        foreach ($comandos as $comando){
            $ticket = $enviador->enviarComando($comando);
            
            $connCentral->query("update comandos set 
            comandos.id_ref_comando = '{$ticket}', 
            comandos.`status` = 'Enviando...',
            comandos.atualizado = '1',
            comandos.data_envio = '".date("Y-m-d H:i:s")."'
            where comandos.id = '{$comando['id']}'");
            
            
            
            \Apoio\Helpers::msg("Comandos", "Enviado comando para {$tecnologia}, numero {$ticket}");
        }
    }else{
        \Apoio\Helpers::msg("Comandos", "Nenhum novo comando a ser enviado!");
    }
}

function atualizaComandos($tecnologia){
    $connCentral = Conexoes::conectarCentral();
    
    switch ($tecnologia){
        case "sighra":
            $atualizador = new \Entrada\Sighra\Comando;
            break;
        default :
            \Apoio\Helpers::msg("Comandos", "A tecnologia {$tecnologia} ainda não possui suporte no software SUI");
    }
    
    $comandosPendentes = $connCentral->query("select * from comandos com where com.`status`='Enviando...' and com.tecnologia = '{$tecnologia}'")->fetchAll();
    
    if(count($comandosPendentes) > 0){
        foreach ($comandosPendentes as $comandosPendente){
            $resposta = $atualizador->atulizaComando($comandosPendente['id_ref_comando'], $comandosPendente['id']);
            if($resposta){
                \Apoio\Helpers::msg("Comandos", "Comando {$tecnologia} com o ticket {$comandosPendente['id_ref_comando']} atualizado");
            }else{
                \Apoio\Helpers::msg("Comandos", "Ticket de comando da {$tecnologia} não foi encontrado");
            }
        }
    }else{
        \Apoio\Helpers::msg("Comandos", "Nenhum comando da tecnologia {$tecnologia} para ser atualizado");
    }
}