<?php
namespace Entrada\Mix;

use Apoio\Conexoes;
/**
 * Classe para manuzeio de dados relacionados a eventos de posições.
 *
 * @author Anderson
 * @since 24/08/2017
 */
class Evento {
    public $ID = null;
    public $Description = null;
    public $EventType = null;
    public $InUse = null;
    public $StartOdo = null;
    public $StartPosition = null;
    public $EndOdo = null;
    public $EndPosition = null;
    public $RecordF3Count = null;
    public $UseWarningMessage = null;
    public $AvtivePosition = null;
    public $RecordTime = null;
    public $AlarmTime = null;
    public $RelayTime = null;
    public $Relay2Time = null;
    public $CriticalTime = null;
    public $ActiveTime = null;
    public $ActiveEndTime = null;
    public $TrackTime = null;
    public $TrackInterval = null;
    public $AlarmState = null;
    public $RelayState = null;
    public $Relay2State = null;
    public $CriticalID = null;
    public $WarningMessage = null;
    public $SummaryType = null;
    public $SummaryID = null;
    public $Priority = null;
    public $EventSaveID = null;
    public $Updated = null;
    public $Notes = null;
    
    
    /**
     * Metodo que limpa e reinsere dados na tabela "Evento" do banco de dados.
     */
    public function atualizarListaEventos(){
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        $connMix = Conexoes::conectarMix();
        $usuario = $connMix->query("select usuario.token from usuario where usuario.usuario = 'krona'")->fetchAll();
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        
        
        
        if(count($usuario) > 0){
            //Monta os dados que são necessários para requisição--------------------------------------------
            $ns = "http://www.mixtelematics.com/WebServices/UnitConfiguration";
            $header = array("Token" => $usuario[0]['token']);
            $body = array("GetEventsList" => "");
            $url = "https://new.api.fm-web.us/webservices/UnitConfigurationWebSvc/EventDescriptionProcessWS.asmx?WSDL";
            //Monta os dados que são necessários para requisição--------------------------------------------
            
            
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            try{
                $req = Requisitor::enviaRequisicao($body, "GetEventsList", $url, $header, $ns);
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
                    $this->atualizarListaEventos();
                }else{
                    $erro = new Erro("Evenro", "atualizarListaEventos", "Erro ao realizar requisição do web service", date("Y-m-d H:i:s"));
                    $erro->registrarErro();
                }
                //Se retornar que token é invalido faz o login para obter um novo token--------------------------------------------------------------------------------------------------------------------
            }
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            
            
            
            
            //Deleta a lista de veiculos antigas e slava a nova lista recuperada no WS---------------------------
            $connMix->query("delete from evento");
            $this->gravarEventos($req['GetEventsListResult']['EventDescription']);
            //Deleta a lista de veiculos antigas e slava a nova lista recuperada no WS---------------------------
            
            
            \Apoio\Helpers::msg("Integracao Mix", count($req['GetEventsListResult']['EventDescription'])." eventos carregados.");
        }else{
            $erro = new Erro("Evento", "aualizarListaEvento", "Conta não encontrada", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
    }
    
    /**
     * Metodo utilizada para realizar a gravação dos eventoss no banco de dados.
     * @param array $listaEventos Lista de objetos "Eventos" para serem gravados.
     * @return Boolean Retorna TRUE se a gravação tiver corrido sem problemas.
     */
    private function gravarEventos($listaEventos){
        //Constroi a base do sql para inserção no banco de dados-----------------
        $sql = "insert into evento("
        . "id, description, event_type, "
        . "in_use, start_odo, start_position, "
        . "end_odo, end_position, record_f3_count, "
        . "use_warning_message, active_position, record_time, "
        . "alarm_time, relay_time, relay2_time, "
        . "critical_time, active_time, active_end_time, "
        . "track_time, track_interval, alarm_state, "
        . "relay_state, relay2_state, critical_id, "
        . "warning_message, sumary_type, sumary_id, "
        . "priority, event_save_id, updated, "
        . "notes) values";
        //Constroi a base do sql para inserção no banco de dados-----------------
        
        
        
        //Monta o resto da query baseada em todos os objetos mandados na lista---------------------------------------------------
        $i = 0;
        foreach ($listaEventos as $evento){
            $i++;
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            $sql .= "'". (isset($evento['ID'])?$evento['ID']:null)."', ";
            $sql .= "'". (isset($evento['Description'])?$evento['Description']:null)."', ";
            $sql .= "'". (isset($evento['EventType'])?$evento['EventType']:null)."', ";
            $sql .= "'". (isset($evento['InUse'])?$evento['InUse']:null)."', ";
            $sql .= "'". (isset($evento['StartOdo'])?$evento['StartOdo']:null)."', ";
            $sql .= "'". (isset($evento['StartPosition'])?$evento['StartPosition']:null)."', ";
            $sql .= "'". (isset($evento['EndOdo'])?$evento['EndOdo']:null)."', ";
            $sql .= "'". (isset($evento['EndPosition'])?$evento['EndPosition']:null)."', ";
            $sql .= "'". (isset($evento['RecordF3Count'])?$evento['RecordF3Count']:null)."', ";
            $sql .= "'". (isset($evento['UseWarningMessage'])?$evento['UseWarningMessage']:null)."', ";
            $sql .= "'". (isset($evento['AvtivePosition'])?$evento['AvtivePosition']:null)."', ";
            $sql .= "'". (isset($evento['RecordTime'])?$evento['RecordTime']:null)."', ";
            $sql .= "'". (isset($evento['AlarmTime'])?$evento['AlarmTime']:null)."', ";
            $sql .= "'". (isset($evento['RelayTime'])?$evento['RelayTime']:null)."', ";
            $sql .= "'". (isset($evento['Relay2Time'])?$evento['Relay2Time']:null)."', ";
            $sql .= "'". (isset($evento['CriticalTime'])?$evento['CriticalTime']:null)."', ";
            $sql .= "'". (isset($evento['ActiveTime'])?$evento['ActiveTime']:null)."', ";
            $sql .= "'". (isset($evento['ActiveEndTime'])?$evento['ActiveEndTime']:null)."', ";
            $sql .= "'". (isset($evento['TrackTime'])?$evento['TrackTime']:null)."', ";
            $sql .= "'". (isset($evento['TrackInterval'])?$evento['TrackInterval']:null)."', ";
            $sql .= "'". (isset($evento['AlarmState'])?$evento['AlarmState']:null)."', ";
            $sql .= "'". (isset($evento['RelayState'])?$evento['RelayState']:null)."', ";
            $sql .= "'". (isset($evento['Relay2State'])?$evento['Relay2State']:null)."', ";
            $sql .= "'". (isset($evento['CriticalID'])?$evento['CriticalID']:null)."', ";
            $sql .= "'". (isset($evento['WarningMessage'])?$evento['WarningMessage']:null)."', ";
            $sql .= "'". (isset($evento['SummaryType'])?$evento['SummaryType']:null)."', ";
            $sql .= "'". (isset($evento['SummaryID'])?$evento['SummaryID']:null)."', ";
            $sql .= "'". (isset($evento['Priority'])?$evento['Priority']:null)."', ";
            $sql .= "'". (isset($evento['EventSaveID'])?$evento['EventSaveID']:null)."', ";
            $sql .= "'". (isset($evento['Updated'])?$evento['Updated']:null)."', ";
            $sql .= "'". (isset($evento['Notes'])?$evento['Notes']:null)."'";
            
            $sql .= ")";
        }
        //Monta o resto da query baseada em todos os objetos mandados na lista---------------------------------------------------
        
        
        
        
        
        //executa a gravação--------------------------------------------------------------------------------------------------
        $conn = Conexoes::conectarMix();
        try{
            $conn->query($sql);
            return true;
        } catch (Exception $ex) {
            $erro = new Erro("Evento", "gravarEventos", "Erro na inserção do sql no banco de dados", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a gravação--------------------------------------------------------------------------------------------------
    }
}
