<?php
namespace Entrada\Sighra;


use Apoio\Conexoes;
use Apoio\Erro;

/**
 * Classe destinada a atualização de lista de pontos do banco de integração sighra.
 *
 * @author Anderson
 * @since 05/09/2017
 */
class Ponto {
    
    /**
     * metodo para realiza a busca da lista de ponto do sighra.
     */
    public function buscarListaPontos(){
        
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        \Apoio\Helpers::msg("Integracao Sighra", "Conectando aos bancos de dados necessarios.");
        $connSighra = Conexoes::conectarSighra();
        $connServidorSighra = Conexoes::conectarServidorSighra();
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        
        
        
        //Deleta toda a lista de pontos existente e grava a lista atualizada de pontos------
        \Apoio\Helpers::msg("Integracao Sighra", "Buscando pontos no servidor da Sighra");
        $connSighra->query("delete from cad_ponto");
        $pontos = $connServidorSighra->query("select * from cad_ponto")->fetchAll();
        //Deleta toda a lista de pontos existente e grava a lista atualizada de pontos------
        
        
        
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        if(count($pontos) > 0){
            $this->gravarPontos($pontos);
        }else{
            \Apoio\Helpers::msg("Integracao Sighra", "Nenhum ponto encontrado!");
        }
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        
        
        
    }
    
    
    /**
     * Metodo utilizado para operaão de gravação no banco de dados do sighra.
     * @param Array $pontos lista de pontos obtidos na consulta ao servidor sighra.
     */
    private function gravarPontos($pontos){
        
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        $sql = "insert into cad_ponto(cpnt_id, cpnt_ttpt_id, cpnt_latitude, cpnt_longitude, cpnt_descricao, cpnt_data_ins, cpnt_data_status, cpnt_ativo, cpnt_uf, cpnt_cidade, cpnt_endereco, cpnt_numero, cpnt_CEP, cpnt_contato, cpnt_telefone, cpnt_cusu_id, cpnt_layer) values";
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
         
        
        //Monta o resto do insert com todos os dados a serem gravados no banco-----------
        $i = 0;
        
        foreach ($pontos as $ponto){
            $i++;
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            
            
            $sql .= "'".$this->limpaDados($ponto['cpnt_id'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_ttpt_id'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_latitude'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_longitude'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_descricao'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_data_ins'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_data_status'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_ativo'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_uf'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_cidade'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_endereco'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_numero'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_CEP'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_contato'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_telefone'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_cusu_id'])."', ";
            $sql .= "'".$this->limpaDados($ponto['cpnt_layer'])."'";
            
            $sql .= ")";
            
            
        }
        //Monat o resto do insert com todos os dados a serem gravados no banco-----------
        

        
        
        //Executa a query------------------------------------------------------------------------------------------------------
        try {
            $conn = Conexoes::conectarSighra();
            $conn->query($sql);
            \Apoio\Helpers::msg("Integracao Sighra", count($pontos). " pontos gravados.");
        } catch (Exception $ex) {
            $erro = new Erro("Ponto", "GravarPontos", "Erro ao executar sql no banco de dados.", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a query------------------------------------------------------------------------------------------------------
        
        
        
    }
    
    private function limpaDados($texto){
       return str_replace(array("'", "--"), array(" ", "-"),$texto);
    }
}
