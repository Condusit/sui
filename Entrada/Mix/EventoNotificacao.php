<?php
namespace Entrada\Mix;

use Apoio\Conexoes;

/**
 * Classe que controla as requisições de eventos ocorridos relacionados as posições.
 *
 * @author Anderson
 * @since 25/08/2017
 */
class EventoNotificacao {
    
    /**
     * Metodo utilizado para buscar notificações de eventos no web service.
     */
    public function buscarEventoNotificacao(){
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        $connMix = Conexoes::conectarMix();
        $usuario = $connMix->query("select usuario.token from usuario where usuario.usuario = 'krona'")->fetchAll();
        $ultimoIdEvento = $connMix->query("select controle.id from controle where controle.nome = 'evento'")->fetchAll();
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        
        
        
        if(count($usuario) > 0){
            //Monta os dados que são necessários para requisição--------------------------------------------
            $ns = "http://mixtelematics.com/WebServices/EventNotification";
            $header = array("Token" => $usuario[0]['token']);
            $body = array("GetNotificationsSinceID" => array("ID" => $ultimoIdEvento[0]['id']));
            $url = "https://new.api.fm-web.us/webservices/EventNotificationWebSvc/EventNotificationWS.asmx?WSDL";
            //Monta os dados que são necessários para requisição--------------------------------------------
            
            
            
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            try{
                $req = Requisitor::enviaRequisicao($body, "GetNotificationsSinceID", $url, $header, $ns);
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
                    $erro = new Erro("Evento", "buscarEventoNotificacao", "Erro ao realizar requisição do web service", date("Y-m-d H:i:s"));
                    $erro->registrarErro();
                }
                //Se retornar que token é invalido faz o login para obter um novo token--------------------------------------------------------------------------------------------------------------------
            }
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            
            
            
            if(isset($req['GetNotificationsSinceIDResult']['EventNotification'])){
                
                //Caso retornar um registro ele precisa ser tratado para entrar no array zero----------
                if(!isset($req['GetNotificationsSinceIDResult']['EventNotification'][0])){
                    $transfere = $req['GetNotificationsSinceIDResult']['EventNotification'];
                    unset($req);
                    $req['GetNotificationsSinceIDResult']['EventNotification'][0] = $transfere;
                    unset($transfere);
                }
                //Caso retornar um registro ele precisa ser tratado para entrar no array zero----------
                
                
                
                //Grava os dados de evento e atualiza referencias de controle para próxima requisição-------------------------------------------------------------------
                $ultimoIdEvento = $this->gravarEventos($req['GetNotificationsSinceIDResult']['EventNotification'], $ultimoIdEvento[0]['id']);
                $connMix->query("update controle set controle.id = '{$ultimoIdEvento}' where controle.nome = 'evento'");
                \Apoio\Helpers::msg("Integracao Mix", count($req['GetNotificationsSinceIDResult']['EventNotification']) ." notificacoes de eventos carregados");
                //Grava os dados de evento e atualiza referencias de controle para próxima requisição-------------------------------------------------------------------
                
                
                $this->buscarEventoNotificacao();
            }else{
                \Apoio\Helpers::msg("Integracao Mix", "Nenhum evento de notificacao encontrado");
            }
        }else{
            $erro = new Erro("Evento", "buscarEventoNotificacao", "Conta não encontrada", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
    }   
    
    
    /**
     * Metodo utilizado para gravar lista de dados no banco de dados.
     * @param array $listaEventos
     * @return int Ultimo id para atualização da referencia.
     */
    private function gravarEventos($listaEventos, $ultimoIdEvento){
        //Constroi a base do sql para inserção no banco de dados-----------------
        $sql = "insert into evento_notificacao(
        id, priority, received_date, 
        event_date, description, vehicle_id, 
        driver_id, event_id, gps_id, 
        value, odometer, speed, 
        is_armed, latitude, longitude) values";
        //Constroi a base do sql para inserção no banco de dados-----------------
        
        
        
        //Monta o resto da query baseada em todos os objetos mandados na lista---------------------------------------------------
        $i = 0;
        
        foreach ($listaEventos as $evento){
            $i++;
            if($ultimoIdEvento < $evento['ID']){
                $ultimoIdEvento = $evento['ID'];
            }
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            $sql .= "'". (isset($evento['ID'])?$evento['ID']:null)."', ";
            $sql .= "'". (isset($evento['Priority'])?$evento['Priority']:null)."', ";
            $sql .= "'". (isset($evento['ReceivedDate'])?$evento['ReceivedDate']:null)."', ";
            $sql .= "'". (isset($evento['EventDate'])?$evento['EventDate']:null)."', ";
            $sql .= "'". (isset($evento['Description'])?$evento['Description']:null)."', ";
            $sql .= "'". (isset($evento['VehicleID'])?$evento['VehicleID']:null)."', ";
            $sql .= "'". (isset($evento['DriverID'])?$evento['DriverID']:null)."', ";
            $sql .= "'". (isset($evento['EventID'])?$evento['EventID']:null)."', ";
            $sql .= "'". (isset($evento['GPSID'])?$evento['GPSID']:null)."', ";
            $sql .= "'". (isset($evento['Value'])?$evento['Value']:null)."', ";
            $sql .= "'". (isset($evento['Odometer'])?$evento['Odometer']:null)."', ";
            $sql .= "'". (isset($evento['Speed'])?$evento['Speed']:null)."', ";
            $sql .= "'". (isset($evento['IsArmed'])?$evento['IsArmed']:null)."', ";
            $sql .= "'". (isset($evento['Latitude'])?$evento['Latitude']:null)."', ";
            $sql .= "'". (isset($evento['Longitude'])?$evento['Longitude']:null)."'";
            
            $sql .= ")";
        }
        //Monta o resto da query baseada em todos os objetos mandados na lista---------------------------------------------------
        
        
        
        
        
        //executa a gravação--------------------------------------------------------------------------------------------------
        $conn = Conexoes::conectarMix();
        try{
            $conn->query($sql);
            return $ultimoIdEvento;
        } catch (Exception $ex) {
            $erro = new Erro("EventoNotificacao", "gravarEventos", "Erro na inserção do sql no banco de dados", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a gravação--------------------------------------------------------------------------------------------------
    }
}
