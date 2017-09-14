<?php
namespace Saida;

use Apoio\Conexoes;

/**
 * Classe que modela o envio de posições para o sistema de controle de jornada. 
 *
 * @author Anderson
 * @since 30/08/2017
 */
class ControleJornada {
    
    
    /**
     * Metodo para busca de Veiculos que estejam na lista do Controle de Jornada.
     * @return array Retorna uma lista de "Tecnologias" concatenadas com "Id's" para validação das posições.
     */
    public function buscarVeiculos(){
        //Realiza a conexão e busca os veiculos na lista-------
        $connCj = Conexoes::conectarControleJornada();
        $veiculos = $connCj->query("select 
        vei.COD_DISPO, 
        vei.TECNOLOGIA
        from veiculos_clientes vei")->fetchAll();
        //Realiza a conexão e busca os veiculos na lista-------
        
        
        
        //concatena os id's e tecnologias e guarda em uma lista para o retorno---
        $listaVei = array();
        
        foreach ($veiculos as $veiculo){
            $id = strtolower($veiculo['COD_DISPO']);
            $tec = strtolower($veiculo['TECNOLOGIA']);
            array_push($listaVei, $id.$tec);
        }
        
        return $listaVei;
        //concatena os id's e tecnologias e guarda em uma lista para o retorno---
    }
    /**
     * Metodo utilizado para mandar o dados de posição para o Controle de Jornada 
     * no formato de dados necessário pelo mesmo.
     * @param Posicao $posicao Objeto "Posicao" com os dados de uma unica posição.
     */
    public function enviaPosicao($posicoes){
        //Conecta com o banco de dados do Controle de Jornada---
        $connCj = Conexoes::conectarControleJornada();
        //Conecta com o banco de dados do Controle de Jornada---
        
        foreach ($posicoes as $posicao){
            //Constroi a sql para execução no banco de dados------------------
            $sql = "insert into pacote_posicao set ";
            $sql .= "datahoraemissao = '".date("Y-m-d H:i:s")."', ";
            $sql .= "datahorapacote = '{$posicao['data_hora_pacote']}', ";
            $sql .= "data_hora_grav_filtro = '".date("Y-m-d H:i:s")."', ";
            $sql .= "idseqmsg = '{$posicao['id_seq_mensagem']}', ";
            $sql .= "idterminal = '{$posicao['id_rastreador']}', ";
            $sql .= "latitude = '{$posicao['latitude']}', ";
            $sql .= "longitude = '{$posicao['longitude']}', ";
            $sql .= "datahoraevento = '{$posicao['data_hora_evento']}', ";
            $sql .= "motId = '{$posicao['id_motorista']}', ";
            $sql .= "mot = '{$posicao['nome_motorista']}', ";
            $sql .= "ignicao = '{$posicao['ignicao']}', ";
            $sql .= "tecnologia = '{$posicao['tecnologia']}', ";
            if($posicao['mensagem_livre'] != ""){
                $sql .= "texto = '{$posicao['mensagem_livre']}', ";
            }else if($posicao['mensagem_macro'] != ""){
                $sql .= "texto = '{$posicao['mensagem_macro']}', ";
            }else{
                $sql .= "texto = '', ";
            }
            $sql .= "macro = '{$posicao['id_macro']}', ";
            $sql .= "velocidade = '{$posicao['velocidade']}'";
            //Constroi a sql para execução no banco de dados------------------

            \Apoio\Helpers::msg("Enviador", "Posicao: {$posicao['id_rastreador']} da {$posicao['tecnologia']} para o {$posicao['programa']}");
            //Executa a query no banco de dados---
            $connCj->query($sql);
            //Executa a query no banco de dados---
        }
        
        
    }
}
