<?php
namespace Entrada\Mix;

use Apoio\Conexoes;
/**
 * Classe que controla a entrada de poisições brutas, ou seja, sem eventos.
 *
 * @author Anderson
 * @since 25/08/2017
 */
class Posicao {
    public $id = null;
    public $idRastreador = null;
    public $placa = null;
    public $idMotorista = null;
    public $nomeMotorista = null;
    public $dataHoraEvento = null;
    public $latitude = null;
    public $longitude = null;
    public $direcao = null;
    public $velocidade = null;
    public $idEvento = null;
    public $nomeEvento = null;
    
    
    /**
     * Metodo para buscar posições no web service
     * @return boolean Retorna TRUE quando terminar de puxar todas as posições disponiveis no web service.
     */
    public function buscarPosicoes(){
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        $connMix = Conexoes::conectarMix();
        $usuario = $connMix->query("select usuario.token from usuario where usuario.usuario = '56122CE BR - MAJONAV'")->fetchAll();
        $ultimoIdPosicao = $connMix->query("select controle.id from controle where controle.nome = 'posicao'")->fetchAll();
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        
        
        
        if(count($usuario) > 0){
            //Monta os dados que são necessários para requisição--------------------------------------------
            $ns = "http://www.omnibridge.com/SDKWebServices/Positioning";
            $header = array("Token" => $usuario[0]['token']);
            $body = array("GetPositionsV2SinceID" => array("fromID" => $ultimoIdPosicao[0]['id']));
            $url = "https://new.api.fm-web.us/webservices/PositioningWebSvc/PositioningWS.asmx?WSDL";
            //Monta os dados que são necessários para requisição--------------------------------------------
            
            
            
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            try{
                $req = Requisitor::enviaRequisicao($body, "GetPositionsV2SinceID", $url, $header, $ns);
            } catch (Exception $ex) {
                //Se retornar que token é invalido faz o login para obter um novo token--------------------------------------------------------------------------------------------------------------------
                $erroToken = "System.Web.Services.Protocols.SoapException: Server was unable to process request. ---> System.Security.Authentication.AuthenticationException: Invalid Token. Please log in.
                at MiX.Web.ContextManager.GetTicket(String AuthenticationToken)
                at MiX.Web.ContextManager.GetContext(String AuthenticationToken)
                at VehicleProcessesWS.GetVehiclesList() in D:\b\2\_work\24\s\WebServices\AssetDataWebSvc\App_Code\VehicleProcessesWS.cs:line 127
                --- End of inner exception stack trace ---";
                
                if($ex->getMessage() == $erroToken){
                    $user = new Usuario();
                    $user->fazerLogin("krona");
                    unset($user);
                    $this->buscarPosicoes();
                }else{
                    $erro = new Erro("Posicao", "buscarPosicoes", "Erro ao realizar requisição do web service", date("Y-m-d H:i:s"));
                    $erro->registrarErro();
                }
                //Se retornar que token é invalido faz o login para obter um novo token--------------------------------------------------------------------------------------------------------------------
            }
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            
            if(isset($req['GetPositionsV2SinceIDResult']['GPSPositionV2'])){
                if(!isset($req['GetPositionsV2SinceIDResult']['GPSPositionV2'][0])){
                    $transfere = $req['GetPositionsV2SinceIDResult']['GPSPositionV2'];
                    unset($req);
                    $req['GetPositionsV2SinceIDResult']['GPSPositionV2'][0] = $transfere;
                    unset($transfere);
                }
                $ultimoIdPosicao = $this->gravarPosicoes($req['GetPositionsV2SinceIDResult']['GPSPositionV2'], $ultimoIdPosicao[0]['id']);
                $connMix->query("update controle set controle.id = '{$ultimoIdPosicao}' where controle.nome = 'posicao'");
                $this->buscarPosicoes();
            }else{
                return true;
            }
        }else{
            $erro = new Erro("Posicoes", "buscarPosicoes", "Conta não encontrada", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
    }
    
    
    /**
     * Metodo utilizado para gravar as posições no banco de dados.
     * @param array $listaPosicoes
     * @param int $ultimoIdPosicao
     * @return int Retorna o ultimo numero de id obtido na requisição ao web service.
     */
    private function gravarPosicoes($listaPosicoes, $ultimoIdPosicao){
        //Constroi a base do sql para inserção no banco de dados-----------------
        $sql = "insert into posicoes(
        id, vehicle_id, driver_id, 
        original_driver_id, block_seq, `time`, 
        latitude, longitude, altitude, 
        heading, satellites, hdop, 
        age_of_reading, distance_since_reading, velocity, 
        is_avl, odometer, coord_valid) values";
        //Constroi a base do sql para inserção no banco de dados-----------------
        
        
        
        //Monta o resto da query baseada em todos os objetos mandados na lista---------------------------------------------------
        $i = 0;
        foreach ($listaPosicoes as $posicao){
            $i++;
            if($ultimoIdPosicao < $posicao['ID']){
                $ultimoIdPosicao = $posicao['ID'];
            }
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            $sql .= "'". (isset($posicao['ID'])?$posicao['ID']:null)."', ";
            $sql .= "'". (isset($posicao['VehicleID'])?$posicao['VehicleID']:null)."', ";
            $sql .= "'". (isset($posicao['DriverID'])?$posicao['DriverID']:null)."', ";
            $sql .= "'". (isset($posicao['OriginalDriverID'])?$posicao['OriginalDriverID']:null)."', ";
            $sql .= "'". (isset($posicao['BlockSeq'])?$posicao['BlockSeq']:null)."', ";
            $sql .= "'". (isset($posicao['Time'])?$posicao['Time']:null)."', ";
            $sql .= "'". (isset($posicao['Latitude'])?$posicao['Latitude']:null)."', ";
            $sql .= "'". (isset($posicao['Longitude'])?$posicao['Longitude']:null)."', ";
            $sql .= "'". (isset($posicao['Altitude'])?$posicao['Altitude']:null)."', ";
            $sql .= "'". (isset($posicao['Heading'])?$posicao['Heading']:null)."', ";
            $sql .= "'". (isset($posicao['Satellites'])?$posicao['Satellites']:null)."', ";
            $sql .= "'". (isset($posicao['HDOP'])?$posicao['HDOP']:null)."', ";
            $sql .= "'". (isset($posicao['AgeOfReading'])?$posicao['AgeOfReading']:null)."', ";
            $sql .= "'". (isset($posicao['DistanceSinceReading'])?$posicao['DistanceSinceReading']:null)."', ";
            $sql .= "'". (isset($posicao['Velocity'])?$posicao['Velocity']:null)."', ";
            $sql .= "'". (isset($posicao['IsAVL'])?$posicao['IsAVL']:null)."', ";
            $sql .= "'". (isset($posicao['Odometer'])?$posicao['Odometer']:null)."', ";
            $sql .= "'". (isset($posicao['CoordValid'])?$posicao['CoordValid']:null)."'";
            
            $sql .= ")";
        }
        //Monta o resto da query baseada em todos os objetos mandados na lista---------------------------------------------------
        
        
        
        
        
        //executa a gravação--------------------------------------------------------------------------------------------------
        $conn = Conexoes::conectarMix();
        try{
            $conn->query($sql);
            \Apoio\Helpers::msg("Integracao Mix", $i." posicoes carregadas!");
            return $ultimoIdPosicao;
        } catch (Exception $ex) {
            $erro = new Erro("Posição", "gravarPosições", "Erro na inserção do sql no banco de dados", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a gravação--------------------------------------------------------------------------------------------------
    }
}
