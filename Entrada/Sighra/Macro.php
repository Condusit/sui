<?php
namespace Entrada\Sighra;


use Apoio\Conexoes;
use Apoio\Erro;

/**
 * Classe destinada a atualização de lista de macros do banco de integração sighra.
 *
 * @author Anderson
 * @since 05/09/2017
 */
class Macro {
    
    /**
     * metodo para realiza a busca da lista de macro do sighra.
     */
    public function buscarListaMacros(){
        
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        \Apoio\Helpers::msg("Integracao Sighra", "Conectando aos bancos de dados necessarios.");
        $connSighra = Conexoes::conectarSighra();
        $connServidorSighra = Conexoes::conectarServidorSighra();
        //Raliza a conexão com os bancos de dados do "sighra" e do "servidro sighra"------------------
        
        
        
        //Deleta toda a lista de macros existente e grava a lista atualizada de macros------
        \Apoio\Helpers::msg("Integracao Sighra", "Buscando macros no servidor da Sighra");
        $connSighra->query("delete from cad_macro");
        $macros = $connServidorSighra->query("select * from cad_macro")->fetchAll();
        //Deleta toda a lista de macros existente e grava a lista atualizada de macros------
        
        
        
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        if(count($macros) > 0){
            $this->gravarListaMacros($macros);
        }else{
            \Apoio\Helpers::msg("Integracao Sighra", "Nenhum macro encontrado!");
        }
        //Grava efetivamente os dados no banco de dados do sighra----------------------------
        
        
        
    }
    
    public function buscarMacros(){
        \Apoio\Helpers::msg("Integracao Sighra", "Realizando conexoes");
        $connSighra = Conexoes::conectarSighra();
        $connServidorSighra = Conexoes::conectarServidorSighra();
        
        while(true){
            \Apoio\Helpers::msg("Integracao Sighra", "Buscando referencia para buscas");
            $referencia = $connSighra->query("select * from controles where controles.id = 'macros'")->fetchAll();
            
            \Apoio\Helpers::msg("Integracao Sighra", "Buscando registros de macros");
            $macros = $connServidorSighra->query("select * from log_macro where log_macro.lmac_id > '{$referencia[0]['ref']}' limit 1000")->fetchAll();
            
            if(count($macros) > 0){
                \Apoio\Helpers::msg("Integracao Sighra", "Gravando registros de macros");
                $this->gravarMacros($macros, $connSighra);
                \Apoio\Helpers::msg("Integracao Sighra", count($macros)." macros gravadas");
            }else{
                \Apoio\Helpers::msg("Integracao Sighra", "Nenhuma macro encontrada");
                break;
            }
        }
        
        
        
        
    }
    
    
    /**
     * Metodo utilizado para operaão de gravação no banco de dados do sighra.
     * @param Array $macros lista de macros obtidos na consulta ao servidor sighra.
     */
    private function gravarListaMacros($macros){
        
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        $sql = "insert into cad_macro(cmac_id, cmac_cgma_id, cmac_num, cmac_nome, cmac_ttma_id, cmac_form, cmac_flow_in, cmac_flow_out_1, cmac_flow_out_2, cmac_flow_out_3, cmac_cusu_id, cmac_descricao, cmac_data_ins, cmac_data_status, cmac_validacao, cmac_schema_left, cmac_schema_top, cmac_schema_width, cmac_schema_height, cmac_zoom_left, cmac_zoom_top, cmac_zoom_right, cmac_zoom_bottom, cmac_ancora_raio, cmac_ancora_reacao, cmac_ign_time, cmac_ign_reacao, cmac_ign_move, cmac_bau_open, cmac_bau_close, cmac_bau_reacao, cmac_logout, cmac_prioridade) values";
        //Constroi a base do insert para gravação no banco de dados---------------------------------------------------------------------------
        
        
        //Monta o resto do insert com todos os dados a serem gravados no banco-----------
        $i = 0;
        
        foreach ($macros as $macro){
            $i++;
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }

            $sql .= "'{$macro['cmac_id']}', ";
            $sql .= "'{$macro['cmac_cgma_id']}', ";
            $sql .= "'{$macro['cmac_num']}', ";
            $sql .= "'{$macro['cmac_nome']}', ";
            $sql .= "'{$macro['cmac_ttma_id']}', ";
            $sql .= "'{$macro['cmac_form']}', ";
            $sql .= "'{$macro['cmac_flow_in']}', ";
            $sql .= "'{$macro['cmac_flow_out_1']}', ";
            $sql .= "'{$macro['cmac_flow_out_2']}', ";
            $sql .= "'{$macro['cmac_flow_out_3']}', ";
            $sql .= "'{$macro['cmac_cusu_id']}', ";
            $sql .= "'{$macro['cmac_descricao']}', ";
            $sql .= "'{$macro['cmac_data_ins']}', ";
            $sql .= "'{$macro['cmac_data_status']}', ";
            $sql .= "'{$macro['cmac_validacao']}', ";
            $sql .= "'{$macro['cmac_schema_left']}', ";
            $sql .= "'{$macro['cmac_schema_top']}', ";
            $sql .= "'{$macro['cmac_schema_width']}', ";
            $sql .= "'{$macro['cmac_schema_height']}', ";
            $sql .= "'{$macro['cmac_zoom_left']}', ";
            $sql .= "'{$macro['cmac_zoom_top']}', ";
            $sql .= "'{$macro['cmac_zoom_right']}', ";
            $sql .= "'{$macro['cmac_zoom_bottom']}', ";
            $sql .= "'{$macro['cmac_ancora_raio']}', ";
            $sql .= "'{$macro['cmac_ancora_reacao']}', ";
            $sql .= "'{$macro['cmac_ign_time']}', ";
            $sql .= "'{$macro['cmac_ign_reacao']}', ";
            $sql .= "'{$macro['cmac_ign_move']}', ";
            $sql .= "'{$macro['cmac_bau_open']}', ";
            $sql .= "'{$macro['cmac_bau_close']}', ";
            $sql .= "'{$macro['cmac_bau_reacao']}', ";
            $sql .= "'{$macro['cmac_logout']}', ";
            $sql .= "'{$macro['cmac_prioridade']}'";
            
            
            $sql .= ")";
        }
        //Monat o resto do insert com todos os dados a serem gravados no banco-----------
        
        
        
        
        
        //Executa a query------------------------------------------------------------------------------------------------------
        try {
            $conn = Conexoes::conectarSighra();
            $conn->query($sql);
            \Apoio\Helpers::msg("Integracao Sighra", count($macros). " macros gravados.");
        } catch (Exception $ex) {
            $erro = new Erro("Macro", "GravarMacros", "Erro ao executar sql no banco de dados.", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a query------------------------------------------------------------------------------------------------------
        
        
        
    }
    
    private function gravarMacros($macros, $connSighra){
        $i = 0;
        $ref = "0";
        $sql = "insert into log_macro(lmac_id, lmac_cvei_id, lmac_sequencia, lmac_tapl_id, lmac_cevt_id, lmac_numero, lmac_nome, lmac_msg, lmac_data_gps, lmac_data_ins, lmac_latitude, lmac_longitude, lmac_cpnt_id, lmac_tsta_id, lmac_cusu_id_abertura, lmac_data_abertura, lmac_cusu_id_status, lmac_data_status, lmac_obs, lmac_ignicao, lmac_velocidade, lmac_nsat, lmac_curso, lmac_info, lmac_dop, lmac_input, lmac_output, lmac_tmco_id, lmac_ttma_id, lmac_relatorio, lmac_validacao, lmac_ancora_raio, lmac_ancora_reacao, lmac_ign_time, lmac_ign_reacao, lmac_bau_open, lmac_bau_close, lmac_bau_reacao, lmac_ign_move, lmac_crua_id, lmac_logout, lmac_prioridade) values";
        
        foreach ($macros as $macro){
            $i++;
            
            if($ref < $macro['lmac_id']){
                $ref = $macro['lmac_id'];
            }
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_cvei_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_sequencia'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_tapl_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_cevt_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_numero'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_nome'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_msg'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_data_gps'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_data_ins'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_latitude'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_longitude'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_cpnt_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_tsta_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_cusu_id_abertura'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_data_abertura'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_cusu_id_status'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_data_status'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_obs'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_ignicao'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_velocidade'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_nsat'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_curso'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_info'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_dop'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_input'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_output'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_tmco_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_ttma_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_relatorio'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_validacao'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_ancora_raio'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_ancora_reacao'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_ign_time'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_ign_reacao'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_bau_open'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_bau_close'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_bau_reacao'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_ign_move'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_crua_id'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_logout'])."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados($macro['lmac_prioridade'])."'";
            
            $sql .= ")";
            
        }
        
        $connSighra->query($sql);
        $connSighra->query("update controles set controles.ref  = '{$ref}' where controles.id = 'macros'");
    }
}
