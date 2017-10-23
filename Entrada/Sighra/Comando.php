<?php
namespace Entrada\Sighra;

use Apoio\Conexoes;

/**
 * Classe que controle o envio de mensagens e comandos
 *
 * @author Anderson
 * @since 05/10/2017
 */
class Comando {
    /**
     * Metodo que envia o comando para a tecnologia
     * @param type $comando Objeto comando com os dados para o envio do comando
     * @return int Retorna o id de ticket da inserção na tabela do sighra
     */
    public function enviarComando($comando){
        
        $connServSighra = Conexoes::conectarServidorSighra();
        
        $sql = "insert into log_comando set ";
        $sql .= "log_comando.lcom_tapl_id = '0',";
        $sql .= "log_comando.lcom_cvei_id = '{$comando['id_terminal']}',";
        $sql .= "log_comando.lcom_ccom_id = '{$comando['codigo']}',";
        $sql .= "log_comando.lcom_tsta_id = '0',";
        $sql .= "log_comando.lcom_data_ins = now(),";
        $sql .= "log_comando.lcom_data_status = null,";
        $sql .= "log_comando.lcom_cusu_id = '2',";
        $sql .= "log_comando.lcom_dados = '{$comando['texto']}',";
        $sql .= "log_comando.lcom_timer = '0',";
        $sql .= "log_comando.lcom_timeout = '0',";
        $sql .= "log_comando.lcom_tmco_id = '1',";
        $sql .= "log_comando.lcom_mclient_nome = 'NULL'";
        
        $connServSighra->query($sql);
        
        return $connServSighra->lastInsertId();
    }
    
    public function atulizaComando($ticket, $id){
        $connServSighra = Conexoes::conectarServidorSighra();
        $connCentral = Conexoes::conectarCentral();
        
        $comando = $connServSighra->query("select 
        sta.tsta_id as id_status,
        sta.tsta_descricao as descricao 
        from log_comando com
        join  tab_status sta on com.lcom_tsta_id = sta.tsta_id
        where
        com.lcom_id = '{$ticket}'")->fetchAll();
        
        if(count($comando) > 0){
            
            if($comando[0]['id_status'] == 3){
                $connCentral->query("update comandos set 
                comandos.`status` = 'Enviado',
                comandos.status_tecnologia = '{$comando[0]['descricao']}',
                comandos.atualizado = '1'
                where
                comandos.id = '{$id}'");
            }elseif($comando[0]['id_status'] < 3){
                $connCentral->query("update comandos set 
                comandos.`status` = 'Enviando...',
                comandos.status_tecnologia = '{$comando[0]['descricao']}',
                comandos.atualizado = '1'
                where
                comandos.id = '{$id}'");
            }else{
                $connCentral->query("update comandos set 
                comandos.`status` = 'Erro',
                comandos.erro = '{{$comando[0]['descricao']}',
                comandos.atualizado = '1'
                where
                comandos.id = '{$id}'");
            }
            return TRUE;
        }else{
            return FALSE;
        }
    }
}
