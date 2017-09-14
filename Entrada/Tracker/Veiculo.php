<?php
namespace Entrada\Tracker;

use Entrada\Tracker\Login;
use Entrada\Tracker\Requisitor;
use Apoio\Conexoes;

/**
 * Classe responsavel por obtenção dos dados de veiculos.
 *
 * @author Anderson
 * @since 11/09/2017
 */
class Veiculo {
    public function buscarVeiculos(){
        $connTracker = Conexoes::conectarTracker();
        
        $login = new Login();
        $token = $login->buscaToken("integracao.krona");
        
        $req = new Requisitor();
        $cabecalho = array('Content-Type: application/json', 'token: '.$token);
        $corpo = null;
        \Apoio\Helpers::msg("Integracao Tracker", "Fazendo requisicao ao webservice");
        $reqResposta = $req->fazerRequisicao("posicoes/ultimaPosicao", $cabecalho, $corpo);
        if($reqResposta['status'] == "NAOPERMITIDO"){
            $connTracker->query("update login set login.dataExpiracao = '0000-00-00 00:00:00' where login.usuario = 'integracao.krona'");
            $token = $login->buscaToken("integracao.krona");
            $cabecalho = array('Content-Type: application/json', 'token: '.$token);
            $reqResposta = $req->fazerRequisicao("posicoes/ultimaPosicao", $cabecalho, $corpo);
        }
        $veiculos = $reqResposta['object'];
        
        foreach ($veiculos as $veiculo){
            $sql = "insert ignore into veiculos set ";
            $sql .=  "id = '".(isset($veiculo['id'])?$veiculo['id']:"")."', ";
            $sql .=  "codigo_rf = '".(isset($veiculo['codigorf'])?$veiculo['codigorf']:"")."', ";
            $sql .=  "odometro_gps = '".(isset($veiculo['odometroGps'])?$veiculo['odometroGps']:"")."', ";
            $sql .=  "data_aquisicao = '".(isset($veiculo['dataAquisicao'])?$veiculo['dataAquisicao']:"")."', ";
            $sql .=  "distancia_km_frete = '".(isset($veiculo['distanciaKmFrete'])?$veiculo['distanciaKmFrete']:"")."', ";
            $sql .=  "km_manual = '".(isset($veiculo['kmManual'])?$veiculo['kmManual']:"")."', ";
            $sql .=  "horimetro_manual = '".(isset($veiculo['horimetroManual'])?$veiculo['horimetroManual']:"")."', ";
            $sql .=  "horimetro_atual = '".(isset($veiculo['horimetroAtual'])?$veiculo['horimetroAtual']:"")."', ";
            $sql .=  "km_atual = '".(isset($veiculo['kmAtual'])?$veiculo['kmAtual']:"")."', ";
            $sql .=  "status_venda = '".(isset($veiculo['statusVenda'])?$veiculo['statusVenda']:"")."', ";
            $sql .=  "data_ativado = '".(isset($veiculo['dataAtivado'])?$veiculo['dataAtivado']:"")."', ";
            $sql .=  "data_cadastrado = '".(isset($veiculo['dataCadastrado'])?$veiculo['dataCadastrado']:"")."', ";
            $sql .=  "data_cancelado = '".(isset($veiculo['dataCancelado'])?$veiculo['dataCancelado']:"")."', ";
            $sql .=  "fuso = '".(isset($veiculo['fuso'])?$veiculo['fuso']:"")."', ";
            $sql .=  "deletado = '".(isset($veiculo['deletado'])?$veiculo['deletado']:"")."', ";
            $sql .=  "status = '".(isset($veiculo['status'])?$veiculo['status']:"")."', ";
            $sql .=  "finalizado = '".(isset($veiculo['finalizado'])?$veiculo['finalizado']:"")."', ";
            $sql .=  "renavam = '".(isset($veiculo['renavam'])?$veiculo['renavam']:"")."', ";
            $sql .=  "vin = '".(isset($veiculo['vin'])?$veiculo['vin']:"")."', ";
            $sql .=  "ano_fabricacao = '".(isset($veiculo['anoFabricacao'])?$veiculo['anoFabricacao']:"")."', ";
            $sql .=  "ano_modelo = '".(isset($veiculo['anoModelo'])?$veiculo['anoModelo']:"")."', ";
            $sql .=  "placa = '".(isset($veiculo['placa'])?$veiculo['placa']:"")."', ";
            $sql .=  "data_instalacao = '".(isset($veiculo['dataInstalacao'])?$veiculo['dataInstalacao']:"")."', ";
            $sql .=  "tipo_monitoramento = '".(isset($veiculo['tipoMonitoramento'])?$veiculo['tipoMonitoramento']:"")."', ";
            $sql .=  "marca = '".(isset($veiculo['marca'])?$veiculo['marca']:"")."', ";
            $sql .=  "modelo = '".(isset($veiculo['modelo'])?$veiculo['modelo']:"")."', ";
            $sql .=  "cor = '".(isset($veiculo['cor'])?$veiculo['cor']:"")."', ";
            $sql .=  "descricao = '".(isset($veiculo['descricao'])?$veiculo['descricao']:"")."', ";
            $sql .=  "frota = '".(isset($veiculo['frota'])?$veiculo['frota']:"")."', ";
            $sql .=  "tipo = '".(isset($veiculo['tipo'])?$veiculo['tipo']:"")."', ";
            $sql .=  "assistencia = '".(isset($veiculo['assistencia'])?$veiculo['assistencia']:"")."', ";
            $sql .=  "usuario_criacao = '".(isset($veiculo['usuarioCriacao'])?$veiculo['usuarioCriacao']:"")."', ";
            $sql .=  "proprietario_id = '".(isset($veiculo['proprietarioId'])?$veiculo['proprietarioId']:"")."', ";
            $sql .=  "proprietario = '".(isset($veiculo['proprietario'])?$veiculo['proprietario']:"")."'";
            
            $connTracker->query($sql);
            
            foreach ($veiculo['dispositivos'] as $dispositivo){
                $sql = "replace into dispositivos set ";
                $sql .= "id = '".(isset($dispositivo['id'])?$dispositivo['id']:"")."', ";
                $sql .= "veiculo_id = '".(isset($veiculo['id'])?$veiculo['id']:"")."', ";
                $sql .= "fabricante_id = '".(isset($dispositivo['fabricanteId'])?$dispositivo['fabricanteId']:"")."', ";
                $sql .= "fabricante = '".(isset($dispositivo['fabricante'])?$dispositivo['fabricante']:"")."', ";
                $sql .= "numero = '".(isset($dispositivo['numero'])?$dispositivo['numero']:"")."', ";
                $sql .= "numero_str = '".(isset($dispositivo['numeroStr'])?$dispositivo['numeroStr']:"")."', ";
                $sql .= "skywave = '".(isset($dispositivo['skywave'])?$dispositivo['skywave']:"")."', ";
                $sql .= "tensao_bateria = '".(isset($dispositivo['tensaoBateria'])?$dispositivo['tensaoBateria']:"")."', ";
                $sql .= "dispositivo_principal = '".(isset($dispositivo['dispositivoPrincipal'])?$dispositivo['dispositivoPrincipal']:"")."', ";
                $sql .= "movel = '".(isset($dispositivo['movel'])?$dispositivo['movel']:"")."', ";
                $sql .= "observacao = '".(isset($dispositivo['observacao'])?$dispositivo['observacao']:"")."', ";
                $sql .= "data_cadastro = '".(isset($dispositivo['dataCadastro'])?$dispositivo['dataCadastro']:"")."', ";
                $sql .= "usuario_cadastro = '".(isset($dispositivo['usuarioCadastro'])?$dispositivo['usuarioCadastro']:"")."', ";
                $sql .= "data_atualizacao_lcc_id = '".(isset($dispositivo['dataAtualizacaoIccid'])?$dispositivo['dataAtualizacaoIccid']:"")."', ";
                $sql .= "inicio_vinculo = '".(isset($dispositivo['inicioVinculo'])?$dispositivo['inicioVinculo']:"")."', ";
                $sql .= "fim_vinculo = '".(isset($dispositivo['fimVinculo'])?$dispositivo['fimVinculo']:"")."', ";
                $sql .= "serial_hexa = '".(isset($dispositivo['serialHexa'])?$dispositivo['serialHexa']:"")."'";
                
                $connTracker->query($sql);
                
                foreach ($dispositivo['chips'] as $chip){
                    $sql = "replace into chips set ";
                    $sql .= "slot = '".(isset($chip['slot'])?$chip['slot']:"")."', ";
                    $sql .= "linha = '".(isset($chip['linha'])?$chip['linha']:"")."', ";
                    $sql .= "serial_chip = '".(isset($chip['serialChip'])?$chip['serialChip']:"")."', ";
                    $sql .= "tipo_chip = '".(isset($chip['tipoChip'])?$chip['tipoChip']:"")."', ";
                    $sql .= "operadora = '".(isset($chip['operadora'])?$chip['operadora']:"")."', ";
                    $sql .= "dispositivo_id = '".(isset($dispositivo['id'])?$dispositivo['id']:"")."'";
                    
                    $connTracker->query($sql);
                }
                
                foreach ($dispositivo['posicoes'] as $posicao){
                    $sql = "replace into posicoes set ";
                    $sql .= "id_dispositivo_posicao = '".$dispositivo['id']."_".$posicao['id']."', ";
                    $sql .= "dispositivo_id = '".(isset($dispositivo['id'])?$dispositivo['id']:"")."', ";
                    $sql .= "online = '".(isset($posicao['online'])?$posicao['online']:"")."', ";
                    $sql .= "evento_id = '".(isset($posicao['eventoId'])?$posicao['eventoId']:"")."', ";
                    $sql .= "evento = '".(isset($posicao['evento'])?$posicao['evento']:"")."', ";
                    $sql .= "sequencia = '".(isset($posicao['sequencia'])?$posicao['sequencia']:"")."', ";
                    $sql .= "referencia = '".(isset($posicao['referencia'])?$posicao['referencia']:"")."', ";
                    $sql .= "data_equipamento = '".(isset($posicao['dataEquipamento'])?$posicao['dataEquipamento']:"")."', ";
                    $sql .= "data_gps = '".(isset($posicao['dataGPS'])?$posicao['dataGPS']:"")."', ";
                    $sql .= "data_gateway = '".(isset($posicao['dataGateway'])?$posicao['dataGateway']:"")."', ";
                    $sql .= "data_processamento = '".(isset($posicao['dataProcessamento'])?$posicao['dataProcessamento']:"")."', ";
                    $sql .= "tipo = '".(isset($posicao['tipo'])?$posicao['tipo']:"")."', ";
                    $sql .= "id = '".(isset($posicao['id'])?$posicao['id']:"")."', ";
                    $sql .= "texto = '".(isset($posicao['texto'])?$posicao['texto']:"")."', ";
                    $sql .= "binario = '".(isset($posicao['binario'])?$posicao['binario']:"")."', ";
                    $sql .= "validade = '".(isset($posicao['validade'])?$posicao['validade']:"")."', ";
                    $sql .= "latitude = '".(isset($posicao['latitude'])?$posicao['latitude']:"")."', ";
                    $sql .= "longitude = '".(isset($posicao['longitude'])?$posicao['longitude']:"")."', ";
                    $sql .= "velocidade = '".(isset($posicao['velocidade'])?$posicao['velocidade']:"")."', ";
                    $sql .= "proa = '".(isset($posicao['proa'])?$posicao['proa']:"")."', ";
                    $sql .= "altitude = '".(isset($posicao['altitude'])?$posicao['altitude']:"")."', ";
                    $sql .= "hdop = '".(isset($posicao['hdop'])?$posicao['hdop']:"")."', ";
                    $sql .= "satelites = '".(isset($posicao['satelites'])?$posicao['satelites']:"")."', ";
                    $sql .= "livre = '".(isset($posicao['livre'])?$posicao['livre']:"")."', ";
                    $sql .= "endereco = '".(isset($posicao['endereco'])?$posicao['endereco']:"")."', ";
                    $sql .= "motorista = '".(isset($posicao['motorista'])?$posicao['motorista']:"")."', ";
                    $sql .= "numero_str = '".(isset($posicao['numerostr'])?$posicao['numerostr']:"")."', ";
                    $sql .= "fabricante = '".(isset($posicao['fabricante'])?$posicao['fabricante']:"")."', ";
                    $sql .= "ignicao = '".(isset($posicao['componentes'][0]['valor'])?$posicao['componentes'][0]['valor']:"")."', ";
                    $sql .= "odometro = '".(isset($posicao['componentes'][2]['valor'])?$posicao['componentes'][2]['valor']:"")."', ";
                    $sql .= "tensao_bateria_backup = '".(isset($posicao['componentes'][4]['valor'])?$posicao['componentes'][4]['valor']:"")."', ";
                    $sql .= "temperatura_rf = '".(isset($posicao['componentes'][23]['valor'])?$posicao['componentes'][23]['valor']:"")."'";
                    
                    $connTracker->query($sql);
                }
            }
        }
        \Apoio\Helpers::msg("Integracao Tracker", count($veiculos)." veiculos encontrados");
    }
}
