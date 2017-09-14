<?php
namespace Entrada\Sighra;

use Apoio\Conexoes;
use Apoio\Erro;

/**
 * Classe responsavel pelo manuzeio de mensagens recebidas.
 *
 * @author Anderson
 * @since 06/09/2017
 */
class Mensagem {
    public function buscarMensagens(){
        
        \Apoio\Helpers::msg("Integracao Sighra", "Conectando com bancos de dados");
        $connSighra = Conexoes::conectarSighra();
        $connServidroSighra = Conexoes::conectarServidorSighra();
        
        
        
        while(true){
            \Apoio\Helpers::msg("Integracao Sighra", "Buscando referencia de busca");
            $referencia = $connSighra->query("select * from controles where controles.id = 'mensagens'")->fetchAll();
            \Apoio\Helpers::msg("Integracao Sighra", "Buscando mensagens");
            $mensagens = $connServidroSighra->query("select * from log_mensagem where log_mensagem.lmsg_id > '{$referencia[0]['ref']}' limit 1000")->fetchAll();
            
            if(count($mensagens) > 0){
                \Apoio\Helpers::msg("Integracao Sighra", "Gravando mensagens");
                $this->gravaMensagens($mensagens, $connSighra);
                \Apoio\Helpers::msg("Integracao Sighra", count($mensagens)." mensagens gravadas");
            }else{
                \Apoio\Helpers::msg("Integracao Sighra", "Nenhuma mensagem encontrada");
                break;
            }
        }
    }
    
    private function gravaMensagens($mensagens, $connSighra){
        $ref = "0";
        $i = 0;
        $sql = "insert into log_mensagem(lmsg_id, lmsg_cvei_id, lmsg_sequencia, lmsg_tapl_id, lmsg_cevt_id, lmsg_data_gps, lmsg_data_ins, lmsg_data_status, lmsg_latitude, lmsg_longitude, lmsg_cpnt_id, lmsg_origem, lmsg_cusu_id, lmsg_data_leitura, lmsg_tsta_id, lmsg_mensagem, lmsg_ignicao, lmsg_velocidade, lmsg_nsat, lmsg_curso, lmsg_info, lmsg_dop, lmsg_input, lmsg_output, lmsg_tmco_id, lmsg_mclient_nome, lmsg_crua_id) values";
        
        foreach ($mensagens as $mensagem){
            $i++;
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            
            if($ref < $mensagem['lmsg_id']){
                $ref = $mensagem['lmsg_id'];
            }
            
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_cvei_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_sequencia'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_tapl_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_cevt_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_data_gps'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_data_ins'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_data_status'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_latitude'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_longitude'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_cpnt_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_origem'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_cusu_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_data_leitura'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_tsta_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_mensagem'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_ignicao'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_velocidade'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_nsat'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_curso'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_info'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_dop'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_input'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_output'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_tmco_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_mclient_nome'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($mensagem['lmsg_crua_id'])."'";
            
            $sql .= ")";
        }
        
        try{
            $connSighra->query($sql);
            $connSighra->query("update controles set controles.ref = '{$ref}' where controles.id = 'mensagens'");
        } catch (Exception $ex) {
            $erro = new Erro("Mensagem", "gravarMensagem", "Erro ao executar o sql de no banco de dados", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        
        
    }
}
