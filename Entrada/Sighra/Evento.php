<?php
namespace Entrada\Sighra;


use Apoio\Conexoes;
use Apoio\Erro;

/**
 * Classe destinada a atualização de lista de eventos do banco de integração sighra.
 *
 * @author Anderson
 * @since 05/09/2017
 */
class Evento {
    
    /**
     * metodo para realiza a busca da lista de eventos do sighra.
     */
    public function buscarListaEventos(){
        
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        \Apoio\Helpers::msg("Integracao Sighra", "Conectando aos bancos de dados necessarios.");
        $connSighra = Conexoes::conectarSighra();
        $connServidorSighra = Conexoes::conectarServidorSighra();
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        
        
        
        //Deleta toda a lista de eventos existente e grava a lista atualizada de eventos------
        \Apoio\Helpers::msg("Integracao Sighra", "Buscando eventos no servidor da Sighra");
        $connSighra->query("delete from cad_evento");
        $eventos = $connServidorSighra->query("select * from cad_evento")->fetchAll();
        //Deleta toda a lista de eventos existente e grava a lista atualizada de eventos------
        
        
        
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        if(count($eventos) > 0){
            $this->gravarEventos($eventos);
        }else{
            \Apoio\Helpers::msg("Integracao Sighra", "Nenhum evento encontrado!");
        }
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        
        
        
    }
    
    
    /**
     * Metodo utilizado para operaão de gravação no banco de dados do sighra.
     * @param Array $eventos lista de eventos obtidos na consulta ao servidor sighra.
     */
    private function gravarEventos($eventos){
        
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        $sql = "insert into cad_evento(cevt_id, cevt_tapl_id, cevt_descricao, cevt_ttev_id, cevt_proibicao, cevt_tsen_id) values";
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        
        
        //Monta o resto do insert com todos os dados a serem gravados no banco-----------
        $i = 0;
        
        foreach ($eventos as $evento){
            $i++;
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }

            $sql .= "'{$evento['cevt_id']}', ";
            $sql .= "'{$evento['cevt_tapl_id']}', ";
            $sql .= "'{$evento['cevt_descricao']}', ";
            $sql .= "'{$evento['cevt_ttev_id']}', ";
            $sql .= "'{$evento['cevt_proibicao']}', ";
            $sql .= "'{$evento['cevt_tsen_id']}'";
            
            $sql .= ")";
        }
        //Monat o resto do insert com todos os dados a serem gravados no banco-----------
        
        
        
        
        
        //Executa a query------------------------------------------------------------------------------------------------------
        try {
            $conn = Conexoes::conectarSighra();
            $conn->query($sql);
            \Apoio\Helpers::msg("Integracao Sighra", count($eventos). " eventos gravados.");
        } catch (Exception $ex) {
            $erro = new Erro("Comando", "GravarComandos", "Erro ao executar sql no banco de dados.", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a query------------------------------------------------------------------------------------------------------
        
        
        
    }
}
