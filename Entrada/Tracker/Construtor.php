<?php
namespace Entrada\Tracker;

use Apoio\Conexoes;

/**
 * Classe responsavel por gerar posições em tabelas separadas por data.
 *
 * @author Anderson
 * @since 12/09/2017
 */
class Construtor {
    public function gerarPosicao(){
        $connTracker = Conexoes::conectarTracker();
        
        $posicoes = $connTracker->query("select * from posicoes limit 1000")->fetchAll();
        if(count($posicoes) > 0){
            $i = 0;
            foreach ($posicoes as $posicao){
                $tabela = $connTracker->query("show tables like '".date("Ymd")."'")->fetchAll();
                if(count($tabela) < 1){
                    $this->criarTabela(date("Ymd"), $connTracker);
                }

                $id = $connTracker->query("select vei.ultimo_id_posicao from veiculos vei 
                join dispositivos dis on dis.veiculo_id = vei.id
                where
                dis.id = '{$posicao['dispositivo_id']}'")->fetchAll();

                if($id[0]['ultimo_id_posicao'] < $posicao['id']){
                    $sql = "insert into tracker.".date("Ymd")." set ";
                    

                    $num_rastreador = $connTracker->query("select dispositivos.numero from dispositivos where dispositivos.id = '{$posicao['dispositivo_id']}'")->fetchAll();
                    $sql .= "numero_rastreador = '{$num_rastreador[0]['numero']}', ";

                    $placa = $connTracker->query("select vei.placa from veiculos vei 
                    join dispositivos dis on dis.veiculo_id = vei.id
                    where
                    dis.id = '{$posicao['dispositivo_id']}'")->fetchAll();
                    $sql .= "placa = '{$placa[0]['placa']}', ";

                    $sql .= "online = '{$posicao['online']}', ";
                    $sql .= "evento_id = '{$posicao['evento_id']}', ";
                    $sql .= "evento = '{$posicao['evento']}', ";
                    $sql .= "sequencia = '{$posicao['sequencia']}', ";
                    $sql .= "referencia = '{$posicao['referencia']}', ";
                    $sql .= "data_equipamento = '{$posicao['data_equipamento']}', ";
                    $sql .= "data_gps = '{$posicao['data_gps']}', ";
                    $sql .= "data_gateway = '{$posicao['data_gateway']}', ";
                    $sql .= "data_processamento = '{$posicao['data_processamento']}', ";
                    $sql .= "tipo = '{$posicao['tipo']}', ";
                    $sql .= "id_seq = '{$posicao['id']}', ";
                    $sql .= "texto = '{$posicao['texto']}', ";
                    $sql .= "binario = '{$posicao['binario']}', ";
                    $sql .= "validade = '{$posicao['validade']}', ";
                    $sql .= "latitude = '{$posicao['latitude']}', ";
                    $sql .= "longitude = '{$posicao['longitude']}', ";
                    $sql .= "velocidade = '{$posicao['velocidade']}', ";
                    $sql .= "proa = '{$posicao['proa']}', ";
                    $sql .= "altitude = '{$posicao['altitude']}', ";
                    $sql .= "hdop = '{$posicao['hdop']}', ";
                    $sql .= "satelites = '{$posicao['satelites']}', ";
                    $sql .= "livre = '{$posicao['livre']}', ";
                    $sql .= "endereco = '{$posicao['endereco']}', ";
                    $sql .= "motorista = '{$posicao['motorista']}', ";
                    $sql .= "numero_str = '{$posicao['numero_str']}', ";
                    $sql .= "fabricante = '{$posicao['fabricante']}', ";
                    $sql .= "ignicao = '{$posicao['ignicao']}', ";
                    $sql .= "odometro = '{$posicao['odometro']}', ";
                    $sql .= "tensao_bateria_backup = '{$posicao['tensao_bateria_backup']}', ";
                    $sql .= "temperatura_rf = '{$posicao['temperatura_rf']}', ";

                    $operadora = $connTracker->query("select chips.operadora from chips 
                    join dispositivos dis on chips.dispositivo_id = dis.id
                    where
                    dis.id = '{$posicao['dispositivo_id']}'")->fetchAll();
                    if(count($operadora) > 0){
                        $sql .= "operadora = '{$operadora[0]['operadora']}'";
                    }else{
                        $sql .= "operadora = ''";
                    }
                    
                    $veiculo_id = $connTracker->query("select vei.id from veiculos vei 
                    join dispositivos dis on dis.veiculo_id = vei.id
                    where
                    dis.id = '{$posicao['dispositivo_id']}'")->fetchAll();
                    
                    $connTracker->query($sql);
                    $i++;
                    $connTracker->query("update veiculos set veiculos.ultimo_id_posicao = '{$posicao['id']}' where veiculos.id = '{$veiculo_id[0]['id']}'");
                    $connTracker->query("delete from posicoes where posicoes.id_dispositivo_posicao = '{$posicao['id_dispositivo_posicao']}'");
                }else{
                    $connTracker->query("delete from posicoes where posicoes.id_dispositivo_posicao = '{$posicao['id_dispositivo_posicao']}'");
                }

            }
            \Apoio\Helpers::msg("Integracao Tracker", $i." posicoes gravadas");
        }else{
            \Apoio\Helpers::msg("Integracao Tracker", "Nenhuma nova posicao encontrada");
        }
        
    }
    
    private function criarTabela($data, $connTracker){
        $sql = "CREATE TABLE `{$data}` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`numero_rastreador` VARCHAR(255) NULL DEFAULT NULL,
	`placa` VARCHAR(255) NULL DEFAULT NULL,
	`online` VARCHAR(255) NULL DEFAULT NULL,
	`evento_id` VARCHAR(255) NULL DEFAULT NULL,
	`evento` VARCHAR(255) NULL DEFAULT NULL,
	`sequencia` VARCHAR(255) NULL DEFAULT NULL,
	`referencia` VARCHAR(255) NULL DEFAULT NULL,
	`data_equipamento` VARCHAR(255) NULL DEFAULT NULL,
	`data_gps` VARCHAR(255) NULL DEFAULT NULL,
	`data_gateway` VARCHAR(255) NULL DEFAULT NULL,
	`data_processamento` VARCHAR(255) NULL DEFAULT NULL,
	`tipo` VARCHAR(255) NULL DEFAULT NULL,
	`id_seq` VARCHAR(255) NULL DEFAULT NULL,
	`texto` VARCHAR(255) NULL DEFAULT NULL,
	`binario` VARCHAR(255) NULL DEFAULT NULL,
	`validade` VARCHAR(255) NULL DEFAULT NULL,
	`latitude` VARCHAR(255) NULL DEFAULT NULL,
	`longitude` VARCHAR(255) NULL DEFAULT NULL,
	`velocidade` VARCHAR(255) NULL DEFAULT NULL,
	`proa` VARCHAR(255) NULL DEFAULT NULL,
	`altitude` VARCHAR(255) NULL DEFAULT NULL,
	`hdop` VARCHAR(255) NULL DEFAULT NULL,
	`satelites` VARCHAR(255) NULL DEFAULT NULL,
	`livre` VARCHAR(255) NULL DEFAULT NULL,
	`endereco` VARCHAR(255) NULL DEFAULT NULL,
	`motorista` VARCHAR(255) NULL DEFAULT NULL,
	`numero_str` VARCHAR(255) NULL DEFAULT NULL,
	`fabricante` VARCHAR(255) NULL DEFAULT NULL,
	`ignicao` VARCHAR(255) NULL DEFAULT NULL,
	`odometro` VARCHAR(255) NULL DEFAULT NULL,
	`tensao_bateria_backup` VARCHAR(255) NULL DEFAULT NULL,
	`temperatura_rf` VARCHAR(255) NULL DEFAULT NULL,
	`operadora` VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
        )
        COLLATE='utf8_general_ci'
        ENGINE=MyISAM
        ;
        ";
        
        $connTracker->query($sql);
    }
}
