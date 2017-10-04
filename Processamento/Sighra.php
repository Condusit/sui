<?php
namespace Processamento;

use Apoio\Conexoes;
use Processamento\Posicao;
/**
 * Classe de tratamento de dados da tracker para o processador central
 *
 * @author Anderson
 * @since 13/09/2017
 */
class Sighra {
    
    /**
     * Metodo que busca os dados no banco Sighra inserção na tabela consolidada 
     * de dados.
     * @return null Retorna null caso tenha havido algum erro ao encontrar a 
     * tabela para consulta.
     */
    public function buscarPosicoes(){
        
        //Faz a conexao com os bancos de dados utilizados----
        \Apoio\Helpers::msg("Buscador", "Fazendo conexoes com bancos de dados");
        $connCentral = Conexoes::conectarCentral();
        $connSighra = Conexoes::conectarSighra();
        //Faz a conexao com os bancos de dados utilizados----
        
        
        //Busca a referencia e verifica se a tabela existe ou se há algum erro de referenecia no banco de dados----------------------------------
        \Apoio\Helpers::msg("Buscador", "Buscando posicoes da tecnologia Sighra");
        $referencia = $connCentral->query("select clr.`data`,clr.id from controles clr where clr.nome = 'sighra'")->fetch();
        if(count($connSighra->query("show tables like '{$referencia['data']}'")->fetchAll()) > 0){
            $posicoes = $connSighra->query("select * from `{$referencia['data']}` as dat where dat.lhis_id > '{$referencia['id']}' limit 1000")->fetchAll();
        }else{
            $this->atualizaReferencia($referencia, $connCentral);
        }
        //Busca a referencia e verifica se a tabela existe ou se há algum erro de referenecia no banco de dados----------------------------------
        
        
        
        
        if(count($posicoes) > 0){
            do{
                $lista = array();
                foreach ($posicoes as $posicao){
                    //Vai armazenando o id mais atualizado------
                    if($referencia['id'] < $posicao['lhis_id']){
                        $referencia['id'] = $posicao['lhis_id'];
                    }
                    //Vai armazenando o id mais atualizado------
                    
                    
                    
                    //Cria um objeto "Posição" e popula com os dados e arazena em uma lista------
                    $pos = new Posicao();
                    
                    $pos->direcao = $posicao['lhis_curso'];
                    $pos->evento = $posicao['evento'];
                    $pos->idEvento = $posicao['lhis_cevt_id'];
                    $pos->idMacro = $posicao['id_macro'];
                    $pos->idMensagemLivre = $posicao['id_mensagem'];
                    $pos->idMotorista = $posicao['id_motorista'];
                    $pos->idRastreador = $posicao['id_rastreador'];
                    $pos->idSeqMensagem = $posicao['lhis_sequencia'];
                    $pos->ignicao = $posicao['lhis_ignicao'];
                    $pos->latitude = $posicao['lhis_latitude'];
                    $pos->longitude = $posicao['lhis_longitude'];
                    $posicao['id_mensagem'] == "0" ? $pos->mensagemLivre = "":$pos->mensagemLivre = $posicao['texto'];
                    $posicao['id_macro'] == "0" ? $pos->mensagemMacro = "" : $pos->mensagemMacro = $posicao['texto'];
                    $pos->nomeMotorista = $posicao['nome_motorista'];
                    $pos->odometro = $posicao['hodometro'];
                    $pos->placa = $posicao['placa'];
                    $pos->velocidade = $posicao['lhis_velocidade'];
                    $pos->tecnologia = "sighra";
                    $pos->dataHoraEvento = $posicao['lhis_data_gps'];
                    $pos->dataHoraPacote = date("Y-m-d H:i:s");
                    $pos->localizacao = str_replace(".", ",", $posicao['distancia'])." Km de {$posicao['cidade']}";
                    $pos->tensaoBateria = "";
                    
                    array_push($lista, $pos);
                    //Cria um objeto "Posição" e popula com os dados e arazena em uma lista------
                }
                
                //Grava os dados dos objetos montados e faz uma nova consulta de dados para procura mais posições--------------------------------------------
                $pos->gravarPosicoes($lista);
                \Apoio\Helpers::msg("Buscador", count($lista). " posicoes encontradas na Sighra");
                $posicoes = $connSighra->query("select * from `{$referencia['data']}` as dat where dat.lhis_id > '{$referencia['id']}' limit 1000")->fetchAll();
                //Grava os dados dos objetos montados e faz uma nova consulta de dados para procura mais posições--------------------------------------------
                
            }while(count($posicoes) > 0);
            
            //Atualiza a referencia de dados no banco-------------------------------------------------------------------------
            \Apoio\Helpers::msg("Buscador", "Atualizando referencias");
            $connCentral->query("update controles set controles.id = '{$referencia['id']}' where controles.nome = 'sighra'");
            //Atualiza a referencia de dados no banco-------------------------------------------------------------------------
        }else{
            $this->atualizaReferencia($referencia, $connCentral);
            \Apoio\Helpers::msg("Buscador", "Nenhuma posicao disponivel na Sighra");
        }
        
    }
    
    private function atualizaReferencia($referencia, $connCentral){
        //Formata a data e adiciona um dia para verificar se exsite a tabela do dia seguinte-------
            $ano = substr($referencia['data'], 0, 4);
            $mes = substr($referencia['data'], 4, 2);
            $dia = substr($referencia['data'], 6, 2);
            $dataFormatada = $dia."-".$mes."-".$ano;
            $novaData = date("Ymd", strtotime("+1 days", strtotime($dataFormatada)));
            $connSighra = Conexoes::conectarTracker();
            if(count($connSighra->query("show tables like '{$novaData}'")->fetchAll())){
                $connCentral->query("update controles set controles.`data` = '{$novaData}', controles.id = '0' where controles.nome = 'sighra'");
            }
            
            //Formata a data e adiciona um dia para verificar se exsite a tabela do dia seguinte-------
    }
    
    private function msTosecs($time){
        $timezone 	= 0; //Alterar quando for horario de verão
        $formata 	= ($time / 1000) + ($timezone * 3600);
        return date('Y-m-d H:i:s', $formata);
    }
}
