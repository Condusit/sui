<?php

namespace Entrada\Sighra;

require_once '../../Config.php';

use Apoio\Conexoes;
use PDO;

/**
 * Classe que controla o processamento das posições obtidas pelo processo de Entrada.
 *
 * @author Anderson
 * @since 24/10/2017
 */
class Processamento{
    
    /**
     * Metodo que executa todo o processamento necessário para o cruzamento das informações e geração de eventos.
     */
    public function executar(){
        
        //Realiza as conexões com os bancos de dados necessários ----------
        $connServSighra = Conexoes::conectarServidorSighra();
        $connSighra = Conexoes::conectarSighra();
        //Realiza as conexões com os bancos de dados necessários ----------
        
        
        
        while(true){
            
            //Busca a lista de veiculos ativos na sighra-----------------------------------------------------------
            $veiculos = $connServSighra->query("select 
            vei.cvei_id as id,
            vei.cvei_placa as placa,
            vei.cvei_equipamento as id_rastreador,
            cma.cmta_id as id_motorista,
            cma.cmta_nome as nome_motorista,
            cvma.cvma_data_status as data_status
            from cad_veiculo vei
            join ctl_veiculo_motorista_ativo cvma on vei.cvei_id = cvma.cvma_cvei_id and cvma.cvma_ativo = 'S'
            join ctl_motorista_ativo cma on cvma.cvma_cmta_id = cma.cmta_id
            order by data_status desc")->fetchAll();
            //Busca a lista de veiculos ativos na sighra-----------------------------------------------------------
            
            
            
            //Busca a lista de eventos que a sighra possiu para cruzamanto------
            $eventos = $connServSighra->query("select
            eve.cevt_id as id,
            eve.cevt_tapl_id as tipoAplicacao,
            eve.cevt_descricao as descricao,
            eve.cevt_ttev_id as tipo
            from cad_evento eve")->fetchAll();
            //Busca a lista de eventos que a sighra possiu para cruzamanto------
            
            
            
            //Tras a lista de todos os pontos de referencia cadastrados na sighra-----
            $pontos = $connServSighra->query("select
            pon.cpnt_id as id,
            pon.cpnt_cidade as cidade,
            pon.cpnt_uf as uf,
            pon.cpnt_latitude as latitude,
            pon.cpnt_longitude as longitude
            from cad_ponto pon")->fetchAll();
            //Tras a lista de todos os pontos de referencia cadastrados na sighra-----
            
            
            
            //Tras a lista de operadoras cadastradas na sighra-----
            $operadoras = $connServSighra->query("select
            ope.tope_id as id,
            ope.tope_descricao as descricao
            from tab_operadora ope")->fetchAll();
            //Tras a lista de operadoras cadastradas na sighra-----
            
            
            
            while(true){
                
                //busca os dados na tabela "posicoes01" e adiciona mais alguns campos na busca para cruzamento de dados---
                $posicoes = $connSighra->query("select 
                pos.*, 
                '' as placa ,
                '' as id_rastreador,
                '' as id_motorista,
                '' as nome_motorista,
                '' as evento,
                '' as cidade,
                '' as uf, 
                '' as distancia,
                '' as operadora,
                '' as rua,
                '' as id_macro,
                '' as id_mensagem,
                '' as id_alarme,
                '' as texto,
                '' as hodometro
                from posicao01 pos limit 1000")->fetchAll();
                //busca os dados na tabela "posicoes01" e adiciona mais alguns campos na busca para cruzamento de dados---
                
                
                
                
                if(count($posicoes) > 0){
                    
                    //Prepara o topo do sql para inserção do banco de dados-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    $apagar = array();
                    $sql = "insert into posicao02(lhis_cvei_id, placa, id_rastreador, id_motorista, nome_motorista, lhis_sequencia, lhis_tapl_id, lhis_curso, lhis_cevt_id, evento, lhis_data_gps, lhis_data_ins, lhis_latitude, lhis_longitude, lhis_cpnt_id, cidade, uf, distancia, lhis_ignicao, lhis_velocidade, lhis_nsat, lhis_info, lhis_dop, lhis_input, lhis_output, lhis_tmco_id, lhis_inAlarme, lhis_chip, operadora, lhis_crua_id, rua, lhis_ext_id, id_macro, id_mensagem, id_alarme, texto, lhis_altitude, lhis_consumo, lhis_ltemp_1, lhis_ltemp_2, lhis_ltemp_3, lhis_ltemp_4, hodometro) values";
                    //Prepara o topo do sql para inserção do banco de dados-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

                    
                    
                    echo "\n";
                    for($i = 0; $i < count($posicoes); $i++){
                        
                        //Procura na lista de veiculos os dados referente a posição corrente--------
                        foreach ($veiculos as $veiculo){
                            if($posicoes[$i]['lhis_cvei_id'] == $veiculo['id']){
                                $posicoes[$i]['placa'] = $veiculo['placa'];
                                $posicoes[$i]['id_rastreador'] = $veiculo['id'];
                                $posicoes[$i]['id_motorista'] = $veiculo['id_motorista'];
                                $posicoes[$i]['nome_motorista'] = $veiculo['nome_motorista'];
                                break;
                            }
                        }
                        //Procura na lista de veiculos os dados referente a posição corrente--------
                        
                        
                        
                        //Procura na lista de eventos os dados referentes a posição corrente------------------------------------------------------------------------------------------------------------------
                        foreach ($eventos as $evento){
                            if($posicoes[$i]['lhis_cevt_id'] == $evento['id'] && $posicoes[$i]['lhis_tapl_id'] == $evento['tipoAplicacao']){
                                $posicoes[$i]['evento'] = $evento['descricao'];
                                if($posicoes[$i]['lhis_cevt_id'] == "1025" && $posicoes[$i]['lhis_ext_id'] != "0"){
                                    $posicoes[$i]['id_mensagem'] = $posicoes[$i]['lhis_ext_id'];
                                    $texto = $connServSighra->query("select men.lmsg_mensagem as mensagem from log_mensagem men where men.lmsg_id = '{$posicoes[$i]['lhis_ext_id']}'")->fetchAll();
                                    $posicoes[$i]['texto'] = $texto[0]['mensagem'];
                                }
                                if($posicoes[$i]['lhis_cevt_id'] == "1024" && $posicoes[$i]['lhis_ext_id'] != "0"){
                                    $posicoes[$i]['id_macro'] = $posicoes[$i]['lhis_ext_id'];
                                    $macro = $connServSighra->query("select mac.lmac_nome as nome, mac.lmac_msg as mensagem from log_macro mac where mac.lmac_id = '{$posicoes[$i]['lhis_ext_id']}'")->fetchAll();
                                    $posicoes[$i]['texto'] = $macro[0]['nome']." ---- ".$macro[0]['mensagem'];
                                }
                                if($posicoes[$i]['lhis_cevt_id'] == "1058" && $posicoes[$i]['lhis_ext_id'] != "0"){
                                    $hodometro = $connServSighra->query("select hod.lhod_hodometro as hodometro from log_hodometro hod where hod.lhod_id = '{$posicoes[$i]['lhis_ext_id']}'")->fetchAll();
                                    $posicoes[$i]['hodometro'] = $hodometro[0]['hodometro'];
                                }
                                if($evento['tipo'] == "2" && $posicoes[$i]['lhis_ext_id'] != "0"){
                                    $posicoes[$i]['id_alarme'] = $posicoes[$i]['lhis_ext_id'];
                                }
                                break;
                            }
                        }
                        //Procura na lista de eventos os dados referentes a posição corrente------------------------------------------------------------------------------------------------------------------
                        
                        
                        
                        
                        //Procura na lista de pontos de referencia os dados da posição corrente e calcula a distancia---------------------------------------------------------------------------------
                        foreach ($pontos as $ponto){
                            if($posicoes[$i]['lhis_cpnt_id'] == $ponto['id']){
                                $posicoes[$i]['cidade'] = $ponto['cidade'];
                                $posicoes[$i]['uf'] = $ponto['uf'];
                                $posicoes[$i]['distancia'] = $this->distancia($posicoes[$i]['lhis_latitude'], $posicoes[$i]['lhis_longitude'], $ponto['latitude'], $ponto['longitude']);
                                break;
                            }
                        }
                        //Procura na lista de pontos de referencia os dados da posição corrente e calcula a distancia---------------------------------------------------------------------------------

                        
                        
                        //Procura na lista de operadoras os dados referentes a posição corrente------
                        foreach ($operadoras as $operadora){
                            if($posicoes[$i]['lhis_chip'] == $operadora['id']){
                                $posicoes[$i]['operadora'] = $operadora['descricao'];
                                break;
                            }
                        }
                        //Procura na lista de operadoras os dados referentes a posição corrente------
                        
                        
                        //Coloca a posição processada na lista para ser deletada----
                        array_push($apagar, $posicoes[$i]['lhis_id']);
                        //Coloca a posição processada na lista para ser deletada----
                        
                        
                        
                        //Insere os dados no montante de sql para ser executado------------------------
                        if($i > 0){
                            $sql .= ",(";
                        }else{
                            $sql .= "(";
                        }

                        $sql .= "'{$posicoes[$i]['lhis_cvei_id']}', ";
                        $sql .= "'{$posicoes[$i]['placa']}', ";
                        $sql .= "'{$posicoes[$i]['id_rastreador']}', ";
                        $sql .= "'{$posicoes[$i]['id_motorista']}', ";
                        $sql .= "'{$posicoes[$i]['nome_motorista']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_sequencia']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_tapl_id']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_curso']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_cevt_id']}', ";
                        $sql .= "'{$posicoes[$i]['evento']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_data_gps']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_data_ins']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_latitude']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_longitude']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_cpnt_id']}', ";
                        $sql .= "'{$posicoes[$i]['cidade']}', ";
                        $sql .= "'{$posicoes[$i]['uf']}', ";
                        $sql .= "'{$posicoes[$i]['distancia']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_ignicao']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_velocidade']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_nsat']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_info']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_dop']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_input']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_output']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_tmco_id']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_inAlarme']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_chip']}', ";
                        $sql .= "'{$posicoes[$i]['operadora']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_crua_id']}', ";
                        $sql .= "'{$posicoes[$i]['rua']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_ext_id']}', ";
                        $sql .= "'{$posicoes[$i]['id_macro']}', ";
                        $sql .= "'{$posicoes[$i]['id_mensagem']}', ";
                        $sql .= "'{$posicoes[$i]['id_alarme']}', ";
                        $sql .= "'{$posicoes[$i]['texto']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_altitude']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_consumo']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_ltemp_1']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_ltemp_2']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_ltemp_3']}', ";
                        $sql .= "'{$posicoes[$i]['lhis_ltemp_4']}', ";
                        $sql .= "'{$posicoes[$i]['hodometro']}'";

                        $sql .= ")";
                        echo "\rProcessando ".($i+1)." posicoes";
                        //Insere os dados no montante de sql para ser executado------------------------
                        
                    }
                    
                    
                    //executa a inserção dos registros na tabela "posição02" e apaga os mesmo da tabela "Posição01"-----
                    $connSighra->query($sql);
                    $a = implode(",", $apagar);
                    $connSighra->query("delete from posicao01 where posicao01.lhis_id in ({$a})");
                    echo "\r";
                    \Apoio\Helpers::msg("Sighra", "Processados: {$i} posicoes!");
                    //executa a inserção dos registros na tabela "posição02" e apaga os mesmo da tabela "Posição01"-----
                    
                    
                }else{
                    
                    //Mensagem caso não encontre nenhuma nova posição a ser processada----
                    \Apoio\Helpers::msg("Sighra", "Nenhuma posicao encontrada");
                    break;
                    //Mensagem caso não encontre nenhuma nova posição a ser processada----
                    
                }
                
            }
            break;
        }
    }
    
    /**
     * Metodo utilizado para fazer o calculo de distancia entre dua posições geograficas.
     * @param float $lat_ini Latitude inicial.
     * @param float $lon_ini Longitude inicial.
     * @param float $lat_fim Latitude final.
     * @param float $lon_fim Longitude final.
     * @return float Retorna a distancia em Kilometros no formato 00.000 Km.
     */
    private function distancia($lat_ini, $lon_ini, $lat_fim, $lon_fim){
        $cateto = $lon_ini - $lon_fim;
        $distancia = sin(deg2rad($lat_ini)) * sin(deg2rad($lat_fim)) + cos(deg2rad($lat_ini)) * cos(deg2rad($lat_fim)) * cos(deg2rad($cateto));
        $distancia = acos($distancia);
        $distancia = rad2deg($distancia);
        $milias = $distancia * 60 * 1.1515;
        $kilometros = $milias * 1.609344;
        return number_format($kilometros, 3);
    }
}

$p = new Processamento();
$p->executar();
