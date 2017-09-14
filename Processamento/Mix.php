<?php
namespace Processamento;

use Apoio\Conexoes;
use Processamento\Posicao;
/**
 * Classe de tratamento de dados da mix para o processador central
 *
 * @author Anderson
 * @since 28/08/2017
 */
class Mix {
    
    /**
     * Metodo que busca os dados no banco mix inserção na tabela consolidada 
     * de dados.
     * @return null Retorna null caso tenha havido algum erro ao encontrar a 
     * tabela para consulta.
     */
    public function buscarPosicoes(){
        
        //Faz a conexao com os bancos de dados utilizados----
        \Apoio\Helpers::msg("Buscador", "Fazendo conexoes com bancos de dados");
        $connCentral = Conexoes::conectarCentral();
        $connMix = Conexoes::conectarMix();
        //Faz a conexao com os bancos de dados utilizados----
        
        
        //Busca a referencia e verifica se a tabela existe ou se há algum erro de referenecia no banco de dados----------------------------------
        \Apoio\Helpers::msg("Buscador", "Buscando posicoes da tecnologia Mix");
        $referencia = $connCentral->query("select clr.`data`,clr.id from controles clr where clr.nome = 'mix'")->fetch();
        if(count($connMix->query("show tables like '{$referencia['data']}'")->fetchAll()) > 0){
            $posicoes = $connMix->query("select * from `{$referencia['data']}` as dat where dat.id > '{$referencia['id']}' limit 1000")->fetchAll();
        }else{
            $this->atualizaReferencia($referencia, $connCentral);
        }
        //Busca a referencia e verifica se a tabela existe ou se há algum erro de referenecia no banco de dados----------------------------------
        
        
        
        
        if(count($posicoes) > 0){
            do{
                $lista = array();
                foreach ($posicoes as $posicao){
                    //Vai armazenando o id mais atualizado------
                    if($referencia['id'] < $posicao['id']){
                        $referencia['id'] = $posicao['id'];
                    }
                    //Vai armazenando o id mais atualizado------
                    
                    
                    
                    //Cria um objeto "Posição" e popula com os dados e arazena em uma lista------
                    $pos = new Posicao();
                    
                    $pos->direcao = $posicao['direcao'];
                    $pos->evento = $posicao['evento'];
                    $pos->idEvento = $posicao['id_evento'];
                    $pos->idMacro = "";
                    $pos->idMensagemLivre = "";
                    $pos->idMotorista = $posicao['id_motorista'];
                    $pos->idRastreador = $posicao['id_rastreador'];
                    $pos->idSeqMensagem = $posicao['id_seq_mensage'];
                    $pos->ignicao = $posicao['ignicao'];
                    $pos->latitude = $posicao['latitude'];
                    $pos->longitude = $posicao['longitude'];
                    $pos->mensagemLivre = "";
                    $pos->mensagemMacro = "";
                    $pos->nomeMotorista = $posicao['nome_motorista'];
                    $pos->odometro = $posicao['odometro'];
                    $pos->placa = $posicao['placa'];
                    $pos->velocidade = $posicao['velocidade'];
                    $pos->tecnologia = "mix";
                    $pos->dataHoraEvento = $posicao['data_hora_evento'];
                    $pos->dataHoraPacote = date("Y-m-d H:i:s");
                    $pos->localizacao = "";
                    $pos->tensaoBateria = "";
                    
                    array_push($lista, $pos);
                    //Cria um objeto "Posição" e popula com os dados e arazena em uma lista------
                }
                
                //Grava os dados dos objetos montados e faz uma nova consulta de dados para procura mais posições--------------------------------------------
                $pos->gravarPosicoes($lista);
                \Apoio\Helpers::msg("Buscador", count($lista). " posicoes encontradas na Mix");
                $posicoes = $connMix->query("select * from `{$referencia['data']}` as dat where dat.id > '{$referencia['id']}' limit 1000")->fetchAll();
                //Grava os dados dos objetos montados e faz uma nova consulta de dados para procura mais posições--------------------------------------------
                
            }while(count($posicoes) > 0);
            
            //Atualiza a referencia de dados no banco-------------------------------------------------------------------------
            \Apoio\Helpers::msg("Buscador", "Atualizando referencias");
            $connCentral->query("update controles set controles.id = '{$referencia['id']}' where controles.nome = 'mix'");
            //Atualiza a referencia de dados no banco-------------------------------------------------------------------------
        }else{
            $this->atualizaReferencia($referencia, $connCentral);
            \Apoio\Helpers::msg("Buscador", "Nenhuma posicao disponivel na Mix");
        }
        
    }
    
    private function atualizaReferencia($referencia, $connCentral){
        //Formata a data e adiciona um dia para verificar se exsite a tabela do dia seguinte-------
            $ano = substr($referencia['data'], 0, 4);
            $mes = substr($referencia['data'], 4, 2);
            $dia = substr($referencia['data'], 6, 2);
            $dataFormatada = $dia."-".$mes."-".$ano;
            $novaData = date("Ymd", strtotime("+1 days", strtotime($dataFormatada)));
            $connMix = Conexoes::conectarMix();
            if(count($connMix->query("show tables like '{$novaData}'")->fetchAll())){
                $connCentral->query("update controles set controles.`data` = '{$novaData}', controles.id = '0' where controles.nome = 'mix'");
            }
            
            //Formata a data e adiciona um dia para verificar se exsite a tabela do dia seguinte-------
    }
}
