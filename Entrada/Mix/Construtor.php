<?php
namespace Entrada\Mix;

use Apoio\Conexoes;

/**
 * Classe responsavel pela construção das tabelas de armazenamento e pela 
 * inserção dos dados na mesma.
 *
 * @author Anderson
 * @since 25/08/2017
 */
class Construtor {
    
    /**
     * Metodo utilizado para busca de posicoes no web service mix.
     */
    public function gerarPosicoes(){
        //Realiza a conexao com banco de dados mix---------------
        $conn = Conexoes::conectarMix();
        //Realiza a conexao com banco de dados mix---------------
        
        
        while(true){
            //Trazer as posições de acordo com o numero de referencia obtido do banco de dados--------------------
            $idReferencia = $conn->query("select controle.id from controle where controle.nome = 'construtor'")->fetchAll();
            
            $posicoes = $conn->query("select 
            pos.id as id,
            pos.vehicle_id as id_rastreador,
            pos.driver_id as id_motorista,
            pos.odometer as odometro,
            pos.latitude as latitude,
            pos.longitude as longitude,
            pos.heading as direcao,
            pos.velocity as velocidade,
            pos.time as data_hora_evento
            from posicoes pos
            where
            pos.id > '{$idReferencia[0]['id']}' order by pos.id limit 1000")->fetchAll();
            //Trazer as posições de acordo com o numero de referencia obtido do banco de dados--------------------

            if(count($posicoes) > 0){
                //buscar dados da placa do veiculo para as posições encontradas-----------------------------------
                for($i = 0; $i < count($posicoes);$i++){
                    $veiculo = $conn->query("select veiculo.description as placa from veiculo where veiculo.id = '{$posicoes[$i]['id_rastreador']}'")->fetchAll();
                    if(count($veiculo) > 0){
                        $posicoes[$i]['placa'] = $veiculo[0]['placa'];
                    }else{
                        $posicoes[$i]['placa'] = "";
                    }
                }
                //buscar dados da placa do veiculo para as posições encontradas-----------------------------------




                //Buscar o nome do motorista nas posicoes----------------------------------------------------------
                for($i = 0; $i < count($posicoes); $i++){
                    $motorista = $conn->query("select motorista.name as nome from motorista where motorista.id = '{$posicoes[$i]['id_motorista']}'")->fetchAll();
                    if(count($motorista) > 0){
                        $posicoes[$i]['nome_motorista'] = $motorista[0]['nome'];
                    }else{
                        $posicoes[$i]['nome_motorista'] = "";
                    }
                }
                //Buscar o nome do motorista nas posicoes----------------------------------------------------------




                //Buscando id de evento e nome dos eventos da posição---------------------------------------------
                for($i = 0; $i < count($posicoes); $i++){
                    $id_evento = $conn->query("select en.event_id as id_evento from evento_notificacao en where en.gps_id = '{$posicoes[$i]['id']}'")->fetchAll();
                    if(count($id_evento) > 0){
                        $posicoes[$i]['id_evento'] = $id_evento[0]['id_evento'];
                        $nome_evento = $conn->query("select eve.description as nome_evento from evento eve where eve.id = '{$id_evento[0]['id_evento']}'")->fetchAll();
                        if(count($nome_evento) > 0){
                            $posicoes[$i]['nome_evento'] = $nome_evento[0]['nome_evento'];
                            switch ($id_evento[0]['id_evento']){
                                case "29600":
                                    $conn->query("update veiculo_ignicao set veiculo_ignicao.ignicao = '1' where veiculo_ignicao.id_veiculo = '{$posicoes[$i]['id_rastreador']}'");
                                    break;
                                case "29601":
                                    $conn->query("update veiculo_ignicao set veiculo_ignicao.ignicao = '0' where veiculo_ignicao.id_veiculo = '{$posicoes[$i]['id_rastreador']}'");
                                    break;
                            }
                        }else{
                            $posicoes[$i]['nome_evento'] = "";
                        }
                    }else{
                        $posicoes[$i]['id_evento'] = "0";
                        $posicoes[$i]['nome_evento'] = "Posição Automatica";
                    }
                    $igncao = $conn->query("select ign.ignicao from veiculo_ignicao ign where ign.id_veiculo = '{$posicoes[$i]['id_rastreador']}'")->fetchAll();
                    $posicoes[$i]['ignicao'] = $igncao[0]['ignicao'];
                }
                //Buscando id de evento e nome dos eventos da posição---------------------------------------------


                //Converte a data de GMT -5 para GMT -3-----------------------------------------------
                for($i = 0; $i < count($posicoes); $i++){
                    $posicoes[$i]['data_hora_evento'] = str_replace("-05:00", "-02:00", $posicoes[$i]['data_hora_evento']);
                    $posicoes[$i]['data_hora_evento'] = gmdate("Y-m-d H:i:s", strtotime($posicoes[$i]['data_hora_evento']));
                }
                //Converte a data de GMT -5 para GMT -3-----------------------------------------------
                
                
                //verifica se a tabela com a data do dia existe e a cria caso não exista e grava as posições--------
                $ultimoIdPosicao = 0;
                foreach ($posicoes as $posicao){
                    $data = date("Ymd");
                    $tabela = $conn->query("show tables like '{$data}'")->fetchAll();
                    if(count($tabela) > 0){
                        $sql = "insert into `{$data}` set 
                        id_seq_mensage = '{$posicao['id']}', 
                        data_hora_evento = '{$posicao['data_hora_evento']}', 
                        id_rastreador = '{$posicao['id_rastreador']}', 
                        placa = '{$posicao['placa']}', 
                        id_motorista = '{$posicao['id_motorista']}', 
                        nome_motorista = '{$posicao['nome_motorista']}', 
                        latitude = '{$posicao['latitude']}', 
                        longitude = '{$posicao['longitude']}', 
                        direcao = '{$posicao['direcao']}', 
                        id_evento = '{$posicao['id_evento']}', 
                        evento = '{$posicao['nome_evento']}', 
                        velocidade = '{$posicao['velocidade']}', 
                        odometro = '{$posicao['odometro']}',
                        ignicao = '{$posicao['ignicao']}'";

                        $conn->query($sql);
                    }else{
                        $sql = "CREATE TABLE `{$data}` (
                        `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
                        `id_seq_mensage` VARCHAR(255) NULL DEFAULT '0',
                        `data_hora_evento` VARCHAR(255) NULL DEFAULT '0',
                        `id_rastreador` VARCHAR(255) NULL DEFAULT '0',
                        `placa` VARCHAR(255) NULL DEFAULT '0',
                        `id_motorista` VARCHAR(255) NULL DEFAULT '0',
                        `nome_motorista` VARCHAR(255) NULL DEFAULT '0',
                        `latitude` VARCHAR(255) NULL DEFAULT '0',
                        `longitude` VARCHAR(255) NULL DEFAULT '0',
                        `direcao` VARCHAR(255) NULL DEFAULT '0',
                        `id_evento` VARCHAR(255) NULL DEFAULT '0',
                        `evento` VARCHAR(255) NULL DEFAULT '0',
                        `velocidade` VARCHAR(255) NULL DEFAULT '0',
                        `odometro` VARCHAR(255) NULL DEFAULT '0',
                        `ignicao` VARCHAR(255) NULL DEFAULT '0',
                        PRIMARY KEY (`id`)
                        )
                        COLLATE='utf8_general_ci'
                        ENGINE=MyISAM
                        ;";

                        $conn->query($sql);

                        $sql = "insert into `{$data}` set 
                        id_seq_mensage = '{$posicao['id']}', 
                        data_hora_evento = '{$posicao['data_hora_evento']}', 
                        id_rastreador = '{$posicao['id_rastreador']}', 
                        placa = '{$posicao['placa']}', 
                        id_motorista = '{$posicao['id_motorista']}', 
                        nome_motorista = '{$posicao['nome_motorista']}', 
                        latitude = '{$posicao['latitude']}', 
                        longitude = '{$posicao['longitude']}', 
                        direcao = '{$posicao['direcao']}', 
                        id_evento = '{$posicao['id_evento']}', 
                        evento = '{$posicao['nome_evento']}', 
                        velocidade = '{$posicao['velocidade']}', 
                        odometro = '{$posicao['odometro']}',
                        ignicao = '{$posicao['ignicao']}'";

                        $conn->query($sql);
                    }
                    if($ultimoIdPosicao < $posicao['id']){
                        $ultimoIdPosicao = $posicao['id'];
                    }
                }
                //verifica se a tabela com a data do dia existe e a cria caso não exista e grava as posições--------

                
                
                //Atualiza o numero de referencia usado para buscar as próximas posições no web service-----------------------
                $conn->query("update controle set controle.id = '{$ultimoIdPosicao}' where controle.nome = 'construtor'");
                //Atualiza o numero de referencia usado para buscar as próximas posições no web service-----------------------

                \Apoio\Helpers::msg("Integracao Mix", $i. " posicoes geradas");
            }else{
                \Apoio\Helpers::msg("Integracao Mix", "Nenhuma posicao nova encontrada");
                break;
            }
        }  
    }
}
