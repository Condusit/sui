<?php
namespace Entrada\Sighra;


use Apoio\Conexoes;
use Apoio\Erro;

/**
 * Classe destinada a atualização de lista de comandos do banco de integração sighra.
 *
 * @author Anderson
 * @since 05/09/2017
 */
class Comando {
    
    /**
     * metodo para realiza a busca da lista de comando do sighra.
     */
    public function buscarListaComandos(){
        
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        $connSighra = Conexoes::conectarSighra();
        $connServidorSighra = Conexoes::conectarServidorSighra();
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        
        
        
        //Deleta toda a lista de comandos existente e grava a lista atualizada de comandos------
        $connSighra->query("delete from cad_comando");
        $comandos = $connServidorSighra->query("select * from cad_comando")->fetchAll();
        //Deleta toda a lista de comandos existente e grava a lista atualizada de comandos------
        
        
        
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        if(count($comandos) > 0){
            $this->gravarComandos($comandos);
        }else{
            \Apoio\Helpers::msg("Integracao Sighra", "Nenhum comando encontrado!");
        }
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        
        
        
    }
    
    
    /**
     * Metodo utilizado para operaão de gravação no banco de dados do sighra.
     * @param Array $comandos lista de comandos obtidos na consulta ao servidor sighra.
     */
    private function gravarComandos($comandos){
        
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        $sql = "insert into cad_comando(ccom_id, ccom_tapl_id, ccom_descricao, ccom_tatu_id, ccom_descricao_atuador, ccom_acao) values";
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        
        
        //Monta o resto do insert com todos os dados a serem gravados no banco-----------
        $i = 0;
        
        foreach ($comandos as $comando){
            $i++;
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }

            $sql .= "'{$comando['ccom_id']}', ";
            $sql .= "'{$comando['ccom_tapl_id']}', ";
            $sql .= "'{$comando['ccom_descricao']}', ";
            $sql .= "'{$comando['ccom_tatu_id']}', ";
            $sql .= "'{$comando['ccom_descricao_atuador']}', ";
            $sql .= "'{$comando['ccom_acao']}'";
            
            $sql .= ")";
        }
        //Monat o resto do insert com todos os dados a serem gravados no banco-----------
        
        
        
        
        
        //Executa a query------------------------------------------------------------------------------------------------------
        try {
            $conn = Conexoes::conectarSighra();
            $conn->query($sql);
            \Apoio\Helpers::msg("Integracao Sighra", count($comandos). " comandos gravados.");
        } catch (Exception $ex) {
            $erro = new Erro("Comando", "GravarComandos", "Erro ao executar sql no banco de dados.", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a query------------------------------------------------------------------------------------------------------
        
        
        
    }
}
