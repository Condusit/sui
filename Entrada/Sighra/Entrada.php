<?php
namespace Entrada\Sighra;

require_once '../../Config.php';

use Apoio\Conexoes;
use Entrada\Sighra\BuscaPosicao;

/**
 * Description of Entrada
 *
 * @author Anderson
 */
class Entrada{
    
    public function executar(){
        $connServSighra = Conexoes::conectarServidorSighra();
        $connSighra = Conexoes::conectarSighra();
        while(true){
            $ref = $connSighra->query("select * from controle where controle.nome = 'sighra'")->fetchAll();
            $refArray = explode(",", $ref[0]['ref']);
            
            $linhas = $connServSighra->query("select lhis_id from log_historico_{$refArray[0]} where lhis_id > '{$refArray[1]}' limit 1000")->fetchAll();
            if(count($linhas) > 0){
                $this->buscarPosicao($refArray[0], $refArray[1]);
                foreach ($linhas as $linha){
                    if($refArray[1] < $linha['lhis_id']){
                        $refArray[1] = $linha['lhis_id'];
                    }
                }
                $connSighra->query("update controle set controle.ref = '{$refArray[0]},{$refArray[1]}' where controle.nome = 'sighra'");
            }else{
                $novaData = date('dmY', strtotime('+1 days', strtotime($refArray[0])));
                $tabela = $connServSighra->query("show tables like 'log_historico_{$novaData}'")->fetchAll();
                if(count($tabela) > 0){
                    $connSighra->query("update controle set controle.ref = '{$novaData},0' where controle.nome = 'sighra'");
                }
                \Apoio\Helpers::msg("Sighra", "Nenhuma posicao encontrada");
                break;
            }
        }
    }
    
    public function buscarPosicao($tabela, $indice){
        $connServidorSighra = Conexoes::conectarServidorSighra();
        $connSighra = Conexoes::conectarSighra();
        
        $posicoes = $connServidorSighra->query("select * from mclient.log_historico_{$tabela} his where his.lhis_id > '{$indice}' limit 1000")->fetchAll();
        
        $sql = "insert into posicao01(lhis_cvei_id, lhis_sequencia, lhis_tapl_id, lhis_curso, lhis_cevt_id, lhis_data_gps, lhis_data_ins, lhis_latitude, lhis_longitude, lhis_cpnt_id, lhis_ignicao, lhis_velocidade, lhis_nsat, lhis_info, lhis_dop, lhis_input, lhis_output, lhis_tmco_id, lhis_inAlarme, lhis_chip, lhis_crua_id, lhis_ext_id, lhis_altitude, lhis_consumo, lhis_ltemp_1, lhis_ltemp_2, lhis_ltemp_3, lhis_ltemp_4)values";
        $i = 0;
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

        $connSighra->query($sql);
        \Apoio\Helpers::msg("Sighra", "Obtido: {$i} posicoes!");
        
        
    }
}

$v = new Entrada();
$v->executar();