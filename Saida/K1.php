<?php
namespace Saida;

use Apoio\Conexoes;

/**
 * Classe utilizada para performar os dados para envio ao software K1.
 *
 * @author Anderson
 * @since 29/08/2017
 */
class K1 {
    
    /**
     * Metodo para buscar a lista de veiculos ativos no sistema K1.
     * @return array Retorna um array de "Id's" e "Tecnologias" concatenadas para forma um id unico.
     */
    public function buscarVeiculos(){
        //Conecta e busca a lista de veiculos no K1------------------------------------
        $connK1Kronaone = Conexoes::conectarK1Kronaone();
        $veiculos = $connK1Kronaone->query("(select 
        vei.id_rastreador as numero_rastreador,
        vei.tecnologia as tecnologia
        from kronaone.veiculos vei
        join kronaone.viagens via on vei.id = via.veiculo_id
        where
        via.`status` not in ('finalizada', 'cancelada', 'rascunho'))

        union

        (select 
        via2.id_localizador1_1 as numero_rastreador,
        via2.localizador1_1 as tecnologia 
        from kronaone.viagens via2
        where
        via2.`status` not in ('finalizada', 'cancelada', 'rascunho') and 
        via2.id_localizador1_1 is not null and
        via2.localizador1_1 is not null and
        via2.id_localizador1_1 != '' and
        via2.localizador1_1 != '')

        union

        (select 
        via2.id_localizador1_2 as numero_rastreador,
        via2.localizador1_2 as tecnologia 
        from kronaone.viagens via2
        where
        via2.`status` not in ('finalizada', 'cancelada', 'rascunho') and 
        via2.id_localizador1_2 is not null and
        via2.localizador1_2 is not null and
        via2.id_localizador1_2 != '' and
        via2.localizador1_2 != '')

        union

        (select 
        via2.id_localizador1_3 as numero_rastreador,
        via2.localizador1_3 as tecnologia 
        from kronaone.viagens via2
        where
        via2.`status` not in ('finalizada', 'cancelada', 'rascunho') and 
        via2.id_localizador1_3 is not null and
        via2.localizador1_3 is not null and
        via2.id_localizador1_3 != '' and
        via2.localizador1_3 != '')

        union

        (select 
        via2.id_localizador2_1 as numero_rastreador,
        via2.localizador2_1 as tecnologia 
        from kronaone.viagens via2
        where
        via2.`status` not in ('finalizada', 'cancelada', 'rascunho') and 
        via2.id_localizador2_1 is not null and
        via2.localizador2_1 is not null and
        via2.id_localizador2_1 != '' and
        via2.localizador2_1 != '')

        union

        (select 
        via2.id_localizador2_2 as numero_rastreador,
        via2.localizador2_2 as tecnologia 
        from kronaone.viagens via2
        where
        via2.`status` not in ('finalizada', 'cancelada', 'rascunho') and 
        via2.id_localizador2_2 is not null and
        via2.localizador2_2 is not null and
        via2.id_localizador2_2 != '' and
        via2.localizador2_2 != '')

        union

        (select 
        via2.id_localizador2_3 as numero_rastreador,
        via2.localizador2_3 as tecnologia 
        from kronaone.viagens via2
        where
        via2.`status` not in ('finalizada', 'cancelada', 'rascunho') and 
        via2.id_localizador2_3 is not null and
        via2.localizador2_3 is not null and
        via2.id_localizador2_3 != '' and
        via2.localizador2_3 != '')

        union

        (select 
        via2.id_localizador3_1 as numero_rastreador,
        via2.localizador3_1 as tecnologia 
        from kronaone.viagens via2
        where
        via2.`status` not in ('finalizada', 'cancelada', 'rascunho') and 
        via2.id_localizador3_1 is not null and
        via2.localizador3_1 is not null and
        via2.id_localizador3_1 != '' and
        via2.localizador3_1 != '')

        union

        (select 
        via2.id_localizador3_2 as numero_rastreador,
        via2.localizador3_2 as tecnologia 
        from kronaone.viagens via2
        where
        via2.`status` not in ('finalizada', 'cancelada', 'rascunho') and 
        via2.id_localizador3_2 is not null and
        via2.localizador3_2 is not null and
        via2.id_localizador3_2 != '' and
        via2.localizador3_2 != '')

        union

        (select 
        via2.id_localizador3_3 as numero_rastreador,
        via2.localizador3_3 as tecnologia 
        from kronaone.viagens via2
        where
        via2.`status` not in ('finalizada', 'cancelada', 'rascunho') and 
        via2.id_localizador3_3 is not null and
        via2.localizador3_3 is not null and
        via2.id_localizador3_3 != '' and
        via2.localizador3_3 != '')")->fetchAll();
        //Conecta e busca a lista de veiculos no K1------------------------------------
        
        
        
        //Realiza a concatenação e retorna a lista de ids de veiculos-----
        $listaVei = array();
        
        foreach ($veiculos as $veiculo){
            $id = strtolower($veiculo['numero_rastreador']);
            $tec = strtolower($veiculo['tecnologia']);
            array_push($listaVei, $id.$tec);
        }
        
        return $listaVei;
        //Realiza a concatenação e retorna a lista de ids de veiculos-----
    }
    /**
     * Metodo que envia os dados de uma posição para as tabelas de eventos do K1.
     * @param Posicoes $posicoes lista de objetos "Posicao" com os dados que serão gravados na tabela de eventos do K1.
     */
    public function enviaPosicao($posicoes){
        //Conecta com os bancos do K1 e recupera o numero de viagem da posição a ser gravada-----
        $connK1Kronaone = Conexoes::conectarK1Kronaone();
        $connK1Integrador = Conexoes::conectarK1Integrador();
        $viagens = $connK1Kronaone->query("select 
        via.id,
        LOWER(vei.tecnologia) as tecnologia,
        vei.id_rastreador,
        vei.id_rastreador_sec,
        LOWER(vei.tecnologia_sec) as tecnologia_sec,
        LOWER(via.localizador1_1) as localizador1_1,
        via.id_localizador1_1,
        LOWER(via.localizador1_2) as localizador1_2,
        via.id_localizador1_2,
        LOWER(via.localizador1_3) as localizador1_3,
        via.id_localizador1_3,
        LOWER(via.localizador2_1) as localizador2_1,
        via.id_localizador2_1,
        LOWER(via.localizador2_2) as localizador2_2,
        via.id_localizador2_2,
        LOWER(via.localizador2_3) as localizador2_3,
        via.id_localizador2_3,
        LOWER(via.localizador3_1) as localizador3_1,
        via.id_localizador3_1,
        LOWER(via.localizador3_2) as localizador3_2,
        via.id_localizador3_2,
        LOWER(via.localizador3_3) as localizador3_3,
        via.id_localizador3_3
        from kronaone.viagens via
        join kronaone.veiculos vei on vei.id = via.veiculo_id
        where
        via.`status` not in ('finalizada', 'cancelada', 'rascunho')")->fetchAll();
        //Conecta com os bancos do K1 e recupera o numero de viagem da posição a ser gravada-----
        
        for ($i = 0; $i < count($posicoes); $i++){
            foreach ($viagens as $viagem){
                if($viagem['tecnologia'] == $posicoes[$i]['tecnologia'] && $viagem['id_rastreador'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['tecnologia_sec'] == $posicoes[$i]['tecnologia'] && $viagem['id_rastreador_sec'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['localizador1_1'] == $posicoes[$i]['tecnologia'] && $viagem['id_localizador1_1'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['localizador1_2'] == $posicoes[$i]['tecnologia'] && $viagem['id_localizador1_2'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['localizador1_3'] == $posicoes[$i]['tecnologia'] && $viagem['id_localizador1_3'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['localizador2_1'] == $posicoes[$i]['tecnologia'] && $viagem['id_localizador2_1'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['localizador2_2'] == $posicoes[$i]['tecnologia'] && $viagem['id_localizador2_2'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['localizador2_3'] == $posicoes[$i]['tecnologia'] && $viagem['id_localizador2_3'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['localizador3_1'] == $posicoes[$i]['tecnologia'] && $viagem['id_localizador3_1'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['localizador3_2'] == $posicoes[$i]['tecnologia'] && $viagem['id_localizador3_2'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
                if($viagem['localizador3_3'] == $posicoes[$i]['tecnologia'] && $viagem['id_localizador3_3'] == $posicoes[$i]['id_rastreador']){
                    $posicoes[$i]['numero_viagem'] = $viagem['id'];
                    break;
                }
            }
        }
        
        foreach ($posicoes as $posicao){
            //Realiza o depara de graus para texto de direção utlizado no sistema do K1---
            if($posicao['direcao'] > 23 && $posicao['direcao'] <= 68){
                $rumo = "NORDESTE";
            }
            if($posicao['direcao'] > 68 && $posicao['direcao'] <= 113){
                $rumo = "LESTE";
            }
            if($posicao['direcao'] > 113 && $posicao['direcao'] <= 158){
                $rumo = "SUDESTE";
            }
            if($posicao['direcao'] > 158 && $posicao['direcao'] <= 203){
                $rumo = "SUL";
            }
            if($posicao['direcao'] > 203 && $posicao['direcao'] <= 248){
                $rumo = "SUDOESTE";
            }
            if($posicao['direcao'] > 248 && $posicao['direcao'] <= 293){
                $rumo = "OESTE";
            }
            if($posicao['direcao'] > 293 && $posicao['direcao'] <= 338){
                $rumo = "NOROESTE";
            }
            if(($posicao['direcao'] > 338 && $posicao['direcao'] <= 360) && ($posicao['direcao'] > 1 and $posicao['direcao'] <= 23)){
                $rumo = "NORTE";
            }
            if($posicao['direcao'] == 0){
                $rumo = "INDISPONIVEL";
            }
            //Realiza o depara de graus para texto de direção utlizado no sistema do K1---





            //Extrutura a sql para o envio dos dados para a tabela de eventos do K1--
            $sql = "insert into {$posicao['tecnologia']}_evento set ";
            $sql .= "numero_viagem = '{$posicao['numero_viagem']}', ";
            $sql .= "equipamento = '', ";
            $sql .= "msg_lida_datahora = '', ";
            $sql .= "msg_lida_usuario = '', ";
            $sql .= "datahoraemissao = '".date("Y-m-d H:i:s")."', ";
            $sql .= "datahorapacote = '{$posicao['data_hora_pacote']}', ";
            $sql .= "data_hora_grav_filtro = '".date("Y-m-d H:i:s")."', ";
            $sql .= "idseqmsg = '{$posicao['id_seq_mensagem']}', ";
            $sql .= "nroseqmsg = '', ";
            $sql .= "destino_televento = 'Rastreador', ";
            $sql .= "origem_televento = 'K1', ";
            $sql .= "codmsg = '{$posicao['id_evento']}', ";
            $sql .= "evento = '{$posicao['evento']}', ";
            $sql .= "tipo_evento = '', ";
            $sql .= "tipomsg = '', ";
            $sql .= "cod_macro = '{$posicao['id_macro']}', ";
            $sql .= "macro_krona = '', ";
            $sql .= "texto = '{$posicao['mensagem_macro']}', ";
            $sql .= "tfrid = '', ";
            $sql .= "idterminal = '{$posicao['id_rastreador']}', ";
            $sql .= "prioridade = '', ";
            $sql .= "latitude = '{$posicao['latitude']}', ";
            $sql .= "longitude = '{$posicao['longitude']}', ";
            $sql .= "mun = '', ";
            $sql .= "uf = '', ";
            $sql .= "rod = '', ";
            $sql .= "rua = '', ";
            $sql .= "validade = '', ";
            $sql .= "datahoraevento = '{$posicao['data_hora_evento']}', ";
            $sql .= "statusveic = '', ";
            $sql .= "intervalo = '', ";
            $sql .= "ignicao = '".($posicao['ignicao'] == "1" ? "LIGADA" : "DESLIGADA")."', ";
            $sql .= "velocidade = '{$posicao['velocidade']}', ";
            $sql .= "rumo = '{$rumo}', ";
            $sql .= "rpm = '', ";
            $sql .= "intervalodif = '', ";
            $sql .= "hodometro = '{$posicao['odometro']}', ";
            $sql .= "dataconexao = '', ";
            $sql .= "temperatura1 = '', ";
            $sql .= "temperatura2 = '', ";
            $sql .= "temperatura3 = '', ";
            $sql .= "localizacao = '{$posicao['localizacao']}', ";
            $sql .= "tecnologia = '{$posicao['tecnologia']}', ";
            $sql .= "operadoracel = ''";
            //Extrutura a sql para o envio dos dados para a tabela de eventos do K1--


            \Apoio\Helpers::msg("Enviador", "Posicao: {$posicao['id_rastreador']} da {$posicao['tecnologia']} para o {$posicao['programa']}");
            //Executa o sql extruturado anteriormente--
            $connK1Integrador->query($sql);
            $connK1Integrador->query("update monitor set 
            monitor.hora_evento = '{$posicao['data_hora_evento']}',
            monitor.ultimo_evento = '".date("Y-m-d H:i:s")."'
            where
            monitor.tecnologia = '{$posicao['tecnologia']}'");
            //Executa o sql extruturado anteriormente--
        }
        
        
    }
    /**
     * Metodo que busca os comandos da tabela de comandos do k1
     */
    public function buscarComandos(){
        //Realiza a Conexão com banco de dados do k1 e com Central------------
        $conn170 = Conexoes::conectarK1Integrador();
        $connCentral = Conexoes::conectarCentral();
        //Realiza a Conexão com banco de dados do k1 e com Central------------
        
        
        //Seleciona todos os comando para serem enviados-----------------------------------------------------
        $comandos = $conn170->query("select * from comandos where comandos.pendencia = '1'")->fetchAll();
        //Seleciona todos os comando para serem enviados-----------------------------------------------------
        
        
        //Se houver comandos novos ele insere na tabela de comandos do SUI---------------------------------------------------------------------------------------------------------------------
        if(count($comandos) > 0){
            $i = 0;
            
            $sql = "replace into comandos(programa, id_ref_programa, codigo, texto, tecnologia, conta, id_terminal, `status`, id_ref_comando, status_tecnologia, tipo) values";
            
            foreach ($comandos as $comando){
                $i++;
                if($i > 1){
                    $sql .= ",(";
                }else{
                    $sql .= "(";
                }
                
                $sql .= "'K1', ";
                $sql .= "'{$comando['id']}', ";
                $sql .= "'{$comando['codigo']}', ";
                $sql .= "'{$comando['texto']}', ";
                $sql .= "'{$comando['tecnologia']}', ";
                $sql .= "'{$comando['servidor']}', ";
                $sql .= "'{$comando['idterminal']}', ";
                $sql .= "'A Enviar', ";
                $sql .= "'', ";
                $sql .= "'', ";
                $sql .= "'{$comando['tipo']}'";
                
                $sql .= ")";
            }
            
            $connCentral->query($sql);
            
            \Apoio\Helpers::msg("Comandos", "Obtidos {$i} comandos!");
        }else{
            \Apoio\Helpers::msg("Comandos", "Nenhum novo comando encontrado no K1");
        }
        //Se houver comandos novos ele insere na tabela de comandos do SUI---------------------------------------------------------------------------------------------------------------------
    }
    /**
     * Metodo que atualiza as condições dos comandos para o k1
     */
    public function atualizaComandos(){
        //Conec com os bancos de dados necessários--------------------------
        $conn170 = Conexoes::conectarK1Integrador();
        $connCentral = Conexoes::conectarCentral();
        //Conec com os bancos de dados necessários--------------------------
        
        
        //Seleciona todos os comandos do k1 que tiverem com o atualizar = 1-----------------------------------
        $comandos = $connCentral->query("select * from comandos com where com.programa = 'k1' and com.atualizado = '1'")->fetchAll();
        //Seleciona todos os comandos do k1 que tiverem com o atualizar = 1-----------------------------------
        
        
        //Atualiza comandos na tabela do k1 de acordo com os mesmo na tebala SUI------------------------------
        if(count($comandos) > 0){
            $i = 0;
            foreach ($comandos as $comando){
                $i++;
                $sql = "update comandos set ";
                $sql .= "status = '{$comando['status']}', ";
                $sql .= "erro = '{$comando['erro']}', ";
                $sql .= "data_envio = '{$comando['data_envio']}', ";
                $sql .= "data_atualizacao = '".date("Y-m-d H:i:s")."', ";
                $sql .= "ticket = '{$comando['id_ref_comando']}', ";
                switch ($comando['status']){
                    case "A Enviar":
                        $sql .= "pendencia = '1' ";
                        break;
                    case "Enviando...":
                        $sql .= "pendencia = '2' ";
                        break;
                    case "Enviado":
                        $sql .= "pendencia = '0' ";
                        break;
                }
                
                $sql .= "where id = '{$comando['id_ref_programa']}'";
                
                $conn170->query($sql);
                $connCentral->query("update comandos set comandos.atualizado = '0' where comandos.id = '{$comando['id']}'");
            }
            \Apoio\Helpers::msg("Comando", "Comandos atualizado: {$i}");
        }else{
            \Apoio\Helpers::msg("Comandos", "Nenhuma atualizacao de comandos k1");
        }
        //Atualiza comandos na tabela do k1 de acordo com os mesmo na tebala SUI------------------------------
    }
}
