<?php
namespace Entrada\Sighra;


use Apoio\Conexoes;
use Apoio\Erro;

/**
 * Classe destinada a atualização de lista de motoristas do banco de integração sighra.
 *
 * @author Anderson
 * @since 05/09/2017
 */
class Motorista {
    
    /**
     * metodo para realiza a busca da lista de motorista do sighra.
     */
    public function buscarListaMotoristas(){
        
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        \Apoio\Helpers::msg("Integracao Sighra", "Conectando aos bancos de dados necessarios.");
        $connSighra = Conexoes::conectarSighra();
        $connServidorSighra = Conexoes::conectarServidorSighra();
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        
        
        
        //Deleta toda a lista de motoristas existente e grava a lista atualizada de motoristas------
        \Apoio\Helpers::msg("Integracao Sighra", "Buscando motoristas no servidor da Sighra");
        $connSighra->query("delete from ctl_motorista_ativo");
        $motoristas = $connServidorSighra->query("select * from ctl_motorista_ativo")->fetchAll();
        //Deleta toda a lista de motoristas existente e grava a lista atualizada de motoristas------
        
        
        
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        if(count($motoristas) > 0){
            $this->gravarMotoristas($motoristas);
        }else{
            \Apoio\Helpers::msg("Integracao Sighra", "Nenhum motorista encontrado!");
        }
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        
        
        
    }
    
    
    /**
     * Metodo utilizado para operaão de gravação no banco de dados do sighra.
     * @param Array $motoristas lista de motoristas obtidos na consulta ao servidor sighra.
     */
    private function gravarMotoristas($motoristas){
        
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        $sql = "insert into ctl_motorista_ativo(cmta_id, cmta_ccli_id, cmta_login, cmta_nome, cmta_apelido, cmta_cpf, cmta_rg, cmta_cnh, cmta_senha, cmta_senha_coacao, cmta_data_vencimento, cmta_data_ins, cmta_data_status, cmta_direcao_desc, cmta_direcao_desc_min, cmta_direcao_alert, cmta_direcao_max, cmta_jornada_desc, cmta_jornada_alert, cmta_jornada_max, cmta_jornada_extra, cmta_jornada_desc_min, cmta_semanal_desc, cmta_semanal_alert, cmta_semanal_max, cmta_proib_alert, cmta_proib_min, cmta_proib_max, cmta_refeicao_min, cmta_refeicao_max, cmta_refeicao_alert, cmta_reserva_alert, cmta_reserva_max, cmta_identificacao, cmta_jornada_extra_alert, cmta_refeicao_tot) values";
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        
        
        //Monta o resto do insert com todos os dados a serem gravados no banco-----------
        $i = 0;
        
        foreach ($motoristas as $motorista){
            $i++;
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            
            
            $sql .= "'".$this->limpaDados($motorista['cmta_id'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_ccli_id'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_login'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_nome'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_apelido'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_cpf'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_rg'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_cnh'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_senha'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_senha_coacao'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_data_vencimento'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_data_ins'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_data_status'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_direcao_desc'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_direcao_desc_min'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_direcao_alert'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_direcao_max'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_jornada_desc'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_jornada_alert'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_jornada_max'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_jornada_extra'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_jornada_desc_min'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_semanal_desc'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_semanal_alert'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_semanal_max'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_proib_alert'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_proib_min'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_proib_max'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_refeicao_min'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_refeicao_max'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_refeicao_alert'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_reserva_alert'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_reserva_max'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_identificacao'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_jornada_extra_alert'])."', ";
            $sql .= "'".$this->limpaDados($motorista['cmta_refeicao_tot'])."'";
            
            $sql .= ")";
            
            
        }
        //Monat o resto do insert com todos os dados a serem gravados no banco-----------
        

        
        
        //Executa a query------------------------------------------------------------------------------------------------------
        try {
            $conn = Conexoes::conectarSighra();
            $conn->query($sql);
            \Apoio\Helpers::msg("Integracao Sighra", count($motoristas). " motoristas gravados.");
        } catch (Exception $ex) {
            $erro = new Erro("Motorista", "GravarMotoristas", "Erro ao executar sql no banco de dados.", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a query------------------------------------------------------------------------------------------------------
        
        
        
    }
    
    private function limpaDados($texto){
       return str_replace(array("'", "--"), array(" ", "-"),$texto);
    }
}
