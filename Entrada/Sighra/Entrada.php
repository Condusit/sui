<?php
namespace Entrada\Sighra;

require_once '../../Config.php';

use Apoio\Conexoes;


/**
 * Classe responsavel por buscar e armazenar dados que vem diretamente da sighra.
 *
 * @author Anderson
 * @since 24/10/2017
 */
class Entrada{
    /**
     * Metodo central que executa a sequencia de busca de dados na sighra.
     */
    public function executar(){
        //Realiza a conexão com os bancos de dados necessários a execução----
        $connServSighra = Conexoes::conectarServidorSighra();
        $connSighra = Conexoes::conectarSighra();
        //Realiza a conexão com os bancos de dados necessários a execução----
        
        
        
        while(true){
            //Seleciona o ultimo numero de referencia gravado no banco de dados para puxar mais posições na sighra---
            $ref = $connSighra->query("select * from controle where controle.nome = 'sighra'")->fetchAll();
            $refArray = explode(",", $ref[0]['ref']);
            //Seleciona o ultimo numero de referencia gravado no banco de dados para puxar mais posições na sighra---
            
            
            
            //Consulta o banco de dados para verificar se tem novas posições-------------------------------------------------------------------
            $linhas = $connServSighra->query("select lhis_id from log_historico_{$refArray[0]} where lhis_id > '{$refArray[1]}' limit 1000")->fetchAll();
            //Consulta o banco de dados para verificar se tem novas posições-------------------------------------------------------------------
            
            
            
            if(count($linhas) > 0){
                
                
                //Busca as posições na sighra e atualiza referencia para a próxima busca------------------------------------------------------
                $this->buscarPosicao($refArray[0], $refArray[1]);
                foreach ($linhas as $linha){
                    if($refArray[1] < $linha['lhis_id']){
                        $refArray[1] = $linha['lhis_id'];
                    }
                }
                $connSighra->query("update controle set controle.ref = '{$refArray[0]},{$refArray[1]}' where controle.nome = 'sighra'");
                //Busca as posições na sighra e atualiza referencia para a próxima busca------------------------------------------------------
                
                
            }else{
                
                //Caso não encontre novas posições verifica na sighra se tem uma nova tabela para procurar posições e atualiza a referencia-----------
                $novaData = date('dmY', strtotime('+1 days', strtotime($refArray[0])));
                $tabela = $connServSighra->query("show tables like 'log_historico_{$novaData}'")->fetchAll();
                if(count($tabela) > 0){
                    $connSighra->query("update controle set controle.ref = '{$novaData},0' where controle.nome = 'sighra'");
                }
                \Apoio\Helpers::msg("Sighra", "Nenhuma posicao encontrada");
                break;
                //Caso não encontre novas posições verifica na sighra se tem uma nova tabela para procurar posições e atualiza a referencia-----------
                
            }
        }
    }
    
    /**
     * Metodo para buscar posições na sighra de acordo com a tabela e indice e grava no banco de  dados krona.
     * @param String $tabela Nome da tabela que deve ser pesquisada na sighra.
     * @param Int $indice Numero de referencia para buscar posições a partir deste numero.
     */
    public function buscarPosicao($tabela, $indice){
        
        //Realiza as conexões necessárias para a execução do metodo-------
        $connServidorSighra = Conexoes::conectarServidorSighra();
        $connSighra = Conexoes::conectarSighra();
        //Realiza as conexões necessárias para a execução do metodo-------
        
        
        
        //Busca as novas posições na sighra de acordo com a tabela e ince limitada a 1000 registros por vez------------------------------------------------------
        $posicoes = $connServidorSighra->query("select * from mclient.log_historico_{$tabela} his where his.lhis_id > '{$indice}' limit 1000")->fetchAll();
        //Busca as novas posições na sighra de acordo com a tabela e ince limitada a 1000 registros por vez------------------------------------------------------
        
        
        //Prepara para o topo do sql para a inserção no banco de dados------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        $sql = "insert into posicao01(lhis_cvei_id, lhis_sequencia, lhis_tapl_id, lhis_curso, lhis_cevt_id, lhis_data_gps, lhis_data_ins, lhis_latitude, lhis_longitude, lhis_cpnt_id, lhis_ignicao, lhis_velocidade, lhis_nsat, lhis_info, lhis_dop, lhis_input, lhis_output, lhis_tmco_id, lhis_inAlarme, lhis_chip, lhis_crua_id, lhis_ext_id, lhis_altitude, lhis_consumo, lhis_ltemp_1, lhis_ltemp_2, lhis_ltemp_3, lhis_ltemp_4)values";
        $i = 0;
        //Prepara para o topo do sql para a inserção no banco de dados------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        
        
        //Monta o resto do sql para inserção do banco de dados--------------
        foreach ($posicoes as $posicao){
            $i++;
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }

            $sql .= "'{$posicao['lhis_cvei_id']}', ";
            $sql .= "'{$posicao['lhis_sequencia']}', ";
            $sql .= "'{$posicao['lhis_tapl_id']}', ";
            $sql .= "'{$posicao['lhis_curso']}', ";
            $sql .= "'{$posicao['lhis_cevt_id']}', ";
            $sql .= "'{$posicao['lhis_data_gps']}', ";
            $sql .= "'{$posicao['lhis_data_ins']}', ";
            $sql .= "'{$posicao['lhis_latitude']}', ";
            $sql .= "'{$posicao['lhis_longitude']}', ";
            $sql .= "'{$posicao['lhis_cpnt_id']}', ";
            $sql .= "'{$posicao['lhis_ignicao']}', ";
            $sql .= "'{$posicao['lhis_velocidade']}', ";
            $sql .= "'{$posicao['lhis_nsat']}', ";
            $sql .= "'{$posicao['lhis_info']}', ";
            $sql .= "'{$posicao['lhis_dop']}', ";
            $sql .= "'{$posicao['lhis_input']}', ";
            $sql .= "'{$posicao['lhis_output']}', ";
            $sql .= "'{$posicao['lhis_tmco_id']}', ";
            $sql .= "'{$posicao['lhis_inAlarme']}', ";
            $sql .= "'{$posicao['lhis_chip']}', ";
            $sql .= "'{$posicao['lhis_crua_id']}', ";
            $sql .= "'{$posicao['lhis_ext_id']}', ";
            $sql .= "'{$posicao['lhis_altitude']}', ";
            $sql .= "'{$posicao['lhis_consumo']}', ";
            $sql .= "'{$posicao['lhis_ltemp_1']}', ";
            $sql .= "'{$posicao['lhis_ltemp_2']}', ";
            $sql .= "'{$posicao['lhis_ltemp_3']}', ";
            $sql .= "'{$posicao['lhis_ltemp_4']}'";

            $sql .= ")";
        }
        //Monta o resto do sql para inserção do banco de dados--------------
        
        
        
        //Executa o comando sql--------------------------------------
        $connSighra->query($sql);
        \Apoio\Helpers::msg("Sighra", "Obtido: {$i} posicoes!");
        //Executa o comando sql--------------------------------------
        
        
    }
}

$v = new Entrada();
$v->executar();