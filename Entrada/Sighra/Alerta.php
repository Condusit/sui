<?php
namespace Entrada\Sighra;

use Apoio\Conexoes;
use Apoio\Erro;

/**
 * Classe responsavel pelo manuzeio de alertas recebidas.
 *
 * @author Anderson
 * @since 06/09/2017
 */
class Alerta {
    public function buscarAlertas(){
        
        \Apoio\Helpers::msg("Integracao Sighra", "Conectando com bancos de dados");
        $connSighra = Conexoes::conectarSighra();
        $connServidroSighra = Conexoes::conectarServidorSighra();
        
        
        
        while(true){
            \Apoio\Helpers::msg("Integracao Sighra", "Buscando referencia de busca");
            $referencia = $connSighra->query("select * from controles where controles.id = 'alertas'")->fetchAll();
            \Apoio\Helpers::msg("Integracao Sighra", "Buscando alertas");
            $alertas = $connServidroSighra->query("select * from log_alarme_alerta where log_alarme_alerta.lala_id > '{$referencia[0]['ref']}' limit 1000")->fetchAll();
            
            if(count($alertas) > 0){
                \Apoio\Helpers::msg("Integracao Sighra", "Gravando alertas");
                $this->gravaAlertas($alertas, $connSighra);
                \Apoio\Helpers::msg("Integracao Sighra", count($alertas)." alertas gravadas");
            }else{
                \Apoio\Helpers::msg("Integracao Sighra", "Nenhum alerta encontrada");
                break;
            }
        }
    }
    
    private function gravaAlertas($alertas, $connSighra){
        $ref = "0";
        $i = 0;
        $sql = "insert into log_alarme_alerta(lala_id, lala_cvei_id, lala_sequencia, lala_tapl_id, lala_cevt_id, lala_data_gps, lala_data_ins, lala_latitude, lala_longitude, lala_cpnt_id, lala_ttev_id, lala_tsta_id, lala_cusu_id_abertura, lala_data_abertura, lala_cusu_id_status, lala_data_status, lala_cmot_id, lala_obs, lala_ignicao, lala_velocidade, lala_nsat, lala_curso, lala_info, lala_dop, lala_input, lala_output, lala_tmco_id, lala_crua_id) values";
        
        foreach ($alertas as $alerta){
            $i++;
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            
            if($ref < $alerta['lala_id']){
                $ref = $alerta['lala_id'];
            }
            
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_cvei_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_sequencia'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_tapl_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_cevt_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_data_gps'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_data_ins'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_latitude'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_longitude'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_cpnt_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_ttev_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_tsta_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_cusu_id_abertura'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_data_abertura'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_cusu_id_status'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_data_status'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_cmot_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_obs'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_ignicao'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_velocidade'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_nsat'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_curso'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_info'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_dop'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_input'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_output'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_tmco_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($alerta['lala_crua_id'])."'";
            
            $sql .= ")";
        }
        
        try{
            $connSighra->query($sql);
            $connSighra->query("update controles set controles.ref = '{$ref}' where controles.id = 'alertas'");
        } catch (Exception $ex) {
            $erro = new Erro("Alerta", "gravarAlerta", "Erro ao executar o sql de no banco de dados", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        
        
    }
}
