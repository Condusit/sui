<?php
namespace Entrada\Sighra;


use Apoio\Conexoes;
use Apoio\Erro;

/**
 * Classe destinada a atualização de lista de veiculos do banco de integração sighra.
 *
 * @author Anderson
 * @since 05/09/2017
 */
class Veiculo {
    
    /**
     * metodo para realiza a busca da lista de veiculo do sighra.
     */
    public function buscarListaVeiculos(){
        
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        \Apoio\Helpers::msg("Integracao Sighra", "Conectando aos bancos de dados necessarios.");
        $connSighra = Conexoes::conectarSighra();
        $connServidorSighra = Conexoes::conectarServidorSighra();
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        
        
        
        //Deleta toda a lista de veiculos existente e grava a lista atualizada de veiculos------
        \Apoio\Helpers::msg("Integracao Sighra", "Buscando veiculos no servidor da Sighra");
        $connSighra->query("delete from cad_veiculo");
        $veiculos = $connServidorSighra->query("select * from cad_veiculo")->fetchAll();
        //Deleta toda a lista de veiculos existente e grava a lista atualizada de veiculos------
        
        
        
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        if(count($veiculos) > 0){
            $this->gravarVeiculos($veiculos);
        }else{
            \Apoio\Helpers::msg("Integracao Sighra", "Nenhum veiculo encontrado!");
        }
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        
        
        
    }
    
    
    /**
     * Metodo utilizado para operaão de gravação no banco de dados do sighra.
     * @param Array $veiculos lista de veiculos obtidos na consulta ao servidor sighra.
     */
    private function gravarVeiculos($veiculos){
        
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        $sql = "insert into cad_veiculo(cvei_id, cvei_ccli_id, cvei_data_ins, cvei_data_status, cvei_ano_fabricacao, cvei_ano_modelo, cvei_cmod_id, cvei_placa, cvei_tcor_id, cvei_descricao, cvei_ttec_id, cvei_equipamento, cvei_satelite, cvei_obs, cvei_pa_off, cvei_pa_on, cvei_pa_sat, cvei_motorista, cvei_senha, cvei_senha_coacao, cvei_ativo, cvei_hibrido, cvei_id_pri, cvei_carreta, cvei_major, cvei_minor, cvei_frota, cvei_velocidade, cvei_tempo_bloqueio, cvei_horimetro_ini, cvei_hodometro_ini, cvei_velocidade_aviso, cvei_velocidade_chuva, cvei_telemetria, cvei_temperatura, cvei_obs_2, cvei_obs_3, cvei_pa_data_status) values";
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
         
        
        //Monta o resto do insert com todos os dados a serem gravados no banco-----------
        $i = 0;
        
        foreach ($veiculos as $veiculo){
            $i++;
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            
            
            $sql .= "'".$this->limpaDados($veiculo['cvei_id'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_ccli_id'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_data_ins'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_data_status'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_ano_fabricacao'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_ano_modelo'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_cmod_id'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_placa'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_tcor_id'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_descricao'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_ttec_id'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_equipamento'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_satelite'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_obs'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_pa_off'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_pa_on'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_pa_sat'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_motorista'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_senha'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_senha_coacao'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_ativo'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_hibrido'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_id_pri'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_carreta'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_major'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_minor'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_frota'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_velocidade'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_tempo_bloqueio'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_horimetro_ini'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_hodometro_ini'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_velocidade_aviso'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_velocidade_chuva'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_telemetria'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_temperatura'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_obs_2'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_obs_3'])."', ";
            $sql .= "'".$this->limpaDados($veiculo['cvei_pa_data_status'])."'";
            
            $sql .= ")";
            
            
        }
        //Monat o resto do insert com todos os dados a serem gravados no banco-----------
        

        
        
        //Executa a query------------------------------------------------------------------------------------------------------
        try {
            $conn = Conexoes::conectarSighra();
            $conn->query($sql);
            \Apoio\Helpers::msg("Integracao Sighra", count($veiculos). " veiculos gravados.");
        } catch (Exception $ex) {
            $erro = new Erro("Veiculo", "GravarVeiculos", "Erro ao executar sql no banco de dados.", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a query------------------------------------------------------------------------------------------------------
        
        
        
    }
    
    private function limpaDados($texto){
       return str_replace(array("'", "--"), array(" ", "-"),$texto);
    }
}
