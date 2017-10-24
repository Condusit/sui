<?php
namespace Entrada\Sighra;

require_once '../../Config.php';

use Apoio\Conexoes;

/**
 * Classe que controla a saida padrão dos dados para armazenamento de backup.
 *
 * @author Anderson
 * @since 24/10/2017
 */
class Saida {
    
    /**
     * Metodo que excuta e armazena dados nas tabelas de historico.
     */
    public function executar(){
        
        //Realiza a conexão com o banco de dados-----
        $connSighra = Conexoes::conectarSighra();
        //Realiza a conexão com o banco de dados-----
        
        while(true){
            
            //Verifica se a tabela que será usada existe----------------------------------------
            $nomeTabela = date("Ymd");
            $tabela = $connSighra->query("show tables like '{$nomeTabela}'")->fetchAll();
            //Verifica se a tabela que será usada existe----------------------------------------
            
            
            
            
            if(count($tabela) > 0){
                
                //seleciona 1000 posições da tabela "posicoes02"-------------------------------------
                $posicoes = $connSighra->query("select * from posicao02 limit 1000")->fetchAll();
                //seleciona 1000 posições da tabela "posicoes02"-------------------------------------
                
                
                
                if(count($posicoes) > 0){
                    
                    //Monta o cabeçalho de sql para insertção dos dados-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    $sql = "insert into `{$nomeTabela}`(lhis_cvei_id, placa, id_rastreador, id_motorista, nome_motorista, lhis_sequencia, lhis_tapl_id, lhis_curso, lhis_cevt_id, evento, lhis_data_gps, lhis_data_ins, lhis_latitude, lhis_longitude, lhis_cpnt_id, cidade, uf, distancia, lhis_ignicao, lhis_velocidade, lhis_nsat, lhis_info, lhis_dop, lhis_input, lhis_output, lhis_tmco_id, lhis_inAlarme, lhis_chip, operadora, lhis_crua_id, rua, lhis_ext_id, id_macro, id_mensagem, id_alarme, texto, lhis_altitude, lhis_consumo, lhis_ltemp_1, lhis_ltemp_2, lhis_ltemp_3, lhis_ltemp_4, hodometro) values";
                    $i = 0;
                    $listaApagar = array();
                    //Monta o cabeçalho de sql para insertção dos dados-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    
                    
                    
                    //Monta o resto do sql com os dados a serem inseridos-----------------------
                    foreach ($posicoes as $posicao){
                        $i++;
                        if($i > 1){
                            $sql .= ",(";
                        }else{
                            $sql .= "(";
                        }
                        $sql .= "'{$posicao['lhis_cvei_id']}', ";
                        $sql .= "'{$posicao['placa']}', ";
                        $sql .= "'{$posicao['id_rastreador']}', ";
                        $sql .= "'{$posicao['id_motorista']}', ";
                        $sql .= "'{$posicao['nome_motorista']}', ";
                        $sql .= "'{$posicao['lhis_sequencia']}', ";
                        $sql .= "'{$posicao['lhis_tapl_id']}', ";
                        $sql .= "'{$posicao['lhis_curso']}', ";
                        $sql .= "'{$posicao['lhis_cevt_id']}', ";
                        $sql .= "'{$posicao['evento']}', ";
                        $sql .= "'{$posicao['lhis_data_gps']}', ";
                        $sql .= "'{$posicao['lhis_data_ins']}', ";
                        $sql .= "'{$posicao['lhis_latitude']}', ";
                        $sql .= "'{$posicao['lhis_longitude']}', ";
                        $sql .= "'{$posicao['lhis_cpnt_id']}', ";
                        $sql .= "'{$posicao['cidade']}', ";
                        $sql .= "'{$posicao['uf']}', ";
                        $sql .= "'{$posicao['distancia']}', ";
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
                        $sql .= "'{$posicao['operadora']}', ";
                        $sql .= "'{$posicao['lhis_crua_id']}', ";
                        $sql .= "'{$posicao['rua']}', ";
                        $sql .= "'{$posicao['lhis_ext_id']}', ";
                        $sql .= "'{$posicao['id_macro']}', ";
                        $sql .= "'{$posicao['id_mensagem']}', ";
                        $sql .= "'{$posicao['id_alarme']}', ";
                        $sql .= "'{$posicao['texto']}', ";
                        $sql .= "'{$posicao['lhis_altitude']}', ";
                        $sql .= "'{$posicao['lhis_consumo']}', ";
                        $sql .= "'{$posicao['lhis_ltemp_1']}', ";
                        $sql .= "'{$posicao['lhis_ltemp_2']}', ";
                        $sql .= "'{$posicao['lhis_ltemp_3']}', ";
                        $sql .= "'{$posicao['lhis_ltemp_4']}', ";
                        $sql .= "'{$posicao['hodometro']}'";
                        
                        $sql .= ")";
                        
                        
                        //Adiciona na lista a posição que será apagada na tebela "posicoes02"----
                        array_push($listaApagar, $posicao['lhis_id']);
                        //Adiciona na lista a posição que será apagada na tebela "posicoes02"----
                        
                        
                    }
                    //Monta o resto do sql com os dados a serem inseridos-----------------------
                    
                    
                    //Executa o sql e deleta os dados que ja foram processados da tabela "Posicoes02"-------
                    $connSighra->query($sql);
                    $apaga = implode(",", $listaApagar);
                    $connSighra->query("delete from posicao02 where posicao02.lhis_id in ({$apaga})");
                    \Apoio\Helpers::msg("Sighra", "Posicoes: {$i} geradas!");
                    //Executa o sql e deleta os dados que ja foram processados da tabela "Posicoes02"-------
                    
                    
                }else{
                    
                    //Mostra a mensagem caso nenhuma posição seja encontrada na tabela posicoes02--
                    \Apoio\Helpers::msg("Sighra", "Nenhuma posicao encontrada");
                    break;
                    //Mostra a mensagem caso nenhuma posição seja encontrada na tabela posicoes02--
                    
                }
            }else{
                
                //Caso a tabela do dia não seja encontrada, então ela será criada------
                $sql = "CREATE TABLE `{$nomeTabela}` (
                `lhis_id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `lhis_cvei_id` INT(10) UNSIGNED NOT NULL,
                `placa` VARCHAR(50) NOT NULL,
                `id_rastreador` VARCHAR(50) NOT NULL,
                `id_motorista` VARCHAR(50) NOT NULL,
                `nome_motorista` VARCHAR(255) NOT NULL,
                `lhis_sequencia` SMALLINT(5) UNSIGNED NOT NULL,
                `lhis_tapl_id` TINYINT(3) UNSIGNED NOT NULL,
                `lhis_curso` SMALLINT(5) UNSIGNED NOT NULL,
                `lhis_cevt_id` SMALLINT(5) UNSIGNED NOT NULL,
                `evento` VARCHAR(80) NOT NULL,
                `lhis_data_gps` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
                `lhis_data_ins` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `lhis_latitude` FLOAT NOT NULL,
                `lhis_longitude` FLOAT NOT NULL,
                `lhis_cpnt_id` INT(10) UNSIGNED NOT NULL,
                `cidade` VARCHAR(80) NOT NULL,
                `uf` VARCHAR(80) NOT NULL,
                `distancia` VARCHAR(80) NOT NULL,
                `lhis_ignicao` CHAR(1) NOT NULL,
                `lhis_velocidade` TINYINT(3) UNSIGNED NOT NULL,
                `lhis_nsat` TINYINT(3) UNSIGNED NOT NULL,
                `lhis_info` SMALLINT(5) UNSIGNED NOT NULL,
                `lhis_dop` TINYINT(3) UNSIGNED NOT NULL,
                `lhis_input` BIGINT(20) NOT NULL,
                `lhis_output` SMALLINT(5) UNSIGNED NOT NULL,
                `lhis_tmco_id` TINYINT(3) UNSIGNED NOT NULL,
                `lhis_inAlarme` CHAR(1) NOT NULL,
                `lhis_chip` TINYINT(3) UNSIGNED NOT NULL,
                `operadora` VARCHAR(50) NOT NULL,
                `lhis_crua_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',
                `rua` VARCHAR(255) NOT NULL DEFAULT '0',
                `lhis_ext_id` INT(10) UNSIGNED NULL DEFAULT NULL,
                `id_macro` INT(10) UNSIGNED NULL DEFAULT NULL,
                `id_mensagem` INT(10) UNSIGNED NULL DEFAULT NULL,
                `id_alarme` INT(10) UNSIGNED NULL DEFAULT NULL,
                `texto` VARCHAR(255) NULL DEFAULT NULL,
                `lhis_altitude` TINYINT(4) NOT NULL DEFAULT '0',
                `lhis_consumo` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
                `lhis_ltemp_1` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
                `lhis_ltemp_2` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
                `lhis_ltemp_3` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
                `lhis_ltemp_4` TINYINT(3) UNSIGNED NULL DEFAULT NULL,
                `hodometro` INT(10) UNSIGNED NULL DEFAULT NULL,
                PRIMARY KEY (`lhis_id`),
                UNIQUE INDEX `lhis_cvei_gps_seq_apl_evt_un_02052017` (`lhis_cvei_id`, `lhis_data_gps`, `lhis_sequencia`, `lhis_tapl_id`, `lhis_cevt_id`),
                INDEX `lhis_data_gps_ix` (`lhis_data_gps`),
                INDEX `lhis_data_ins_ix` (`lhis_data_ins`)
                )
                COLLATE='latin1_swedish_ci'
                ENGINE=MyISAM
                ;
                ";
                $connSighra->query($sql);
                //Caso a tabela do dia não seja encontrada, então ela será criada------
                
            }
        }
    }
}

$s = new Saida();
$s->executar();