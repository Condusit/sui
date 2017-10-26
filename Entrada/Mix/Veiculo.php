<?php
namespace Entrada\Mix;

use Apoio\Conexoes;
/**
 * Classe que realiza funções relacionadas aos veiculos da mix, tais como 
 * requisições e cadastros em banco de dados.
 *
 * @author Anderson
 * @since 22/08/2017
 */
class Veiculo {
    public $siteId = null;
    public $groupId = null;
    public $description = null;
    public $registrationNumber = null;
    public $unitType = null;
    public $vehicleIcon = null;
    public $targetFuelConsumption = null;
    public $targetHourlyConsumption = null;
    public $defaultDriverId = null;
    public $lastUpdate = null;
    public $distanceChecked = null;
    public $lastOdometer = null;
    public $lastTrip = null;
    public $lastEngineSeconds = null;
    public $lastServiceKey = null;
    public $nextServiceKm = null;
    public $serviceKm = null;
    public $remindKm = null;
    public $remindKm2 = null;
    public $nextServiceDate = null;
    public $serviceMonths = null;
    public $remindWeek = null;
    public $remindWeek2 = null;
    public $nextServiceSeconds = null;
    public $serviceSeconds = null;
    public $remindSeconds = null;
    public $remindSeconds2 = null;
    public $lastLicence = null;
    public $nextLicence = null;
    public $licenceInterval = null;
    public $licenceRemind = null;
    public $licenceRemind2 = null;
    public $lastCor = null;
    public $nextCor = null;
    public $corInterval = null;
    public $corRemind = null;
    public $corRemind2 = null;
    public $ignoreLateDownload = null;
    public $remindOnDistance = null;
    public $remindOnDuration = null;
    public $serviceHours = null;
    public $licence = null;
    public $cor = null;
    public $defaultAllowDriverAccess = null;
    public $created = null;
    public $active = null;
    public $activeReason = null;
    public $activeState = null;
    
    /**
     * Metodo que limpa e reinsere dados na tabela "Veiculo" do banco de dados.
     * @return boolean Retorna TRUE se toda a tualização correr sem problemas.
     */
    public function atualizarListaVeiculos(){
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        $connMix = Conexoes::conectarMix();
        $usuario = $connMix->query("select usuario.token from usuario where usuario.usuario = '56122CE BR - MAJONAV'")->fetchAll();
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        
        
        
        if(count($usuario) > 0){
            //Monta os dados que são necessários para requisição--------------------------------------------
            $ns = "http://www.omnibridge.com/SDKWebServices/AssetData";
            $header = array("Token" => $usuario[0]['token']);
            $body = array("GetVehiclesList" => "");
            $url = "https://new.api.fm-web.us/webservices/AssetDataWebSvc/VehicleProcessesWS.asmx?WSDL";
            //Monta os dados que são necessários para requisição--------------------------------------------
            
            
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            
                $req = Requisitor::enviaRequisicao($body, "GetVehiclesList", $url, $header, $ns);
                
                if($req instanceof \Exception){
                    //Se retornar que token é invalido faz o login para obter um novo token--------------------------------------------------------------------------------------------------------------------
                    if(strripos($req->getMessage(), "(String AuthenticationToken)") != FALSE){
                        $user = new Usuario();
                        $user->fazerLogin("56122CE BR - MAJONAV");
                        unset($user);
                        $this->atualizarListaVeiculos();
                    }else{
                        $erro = new Erro("Veiculo", "atualizarListaVeiculos", "Erro ao realizar requisição do web service", date("Y-m-d H:i:s"));
                        $erro->registrarErro();
                    }
                    //Se retornar que token é invalido faz o login para obter um novo token--------------------------------------------------------------------------------------------------------------------
                }
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            
            
            
            
            //Deleta a lista de veiculos antigas e slava a nova lista recuperada no WS---------------------------
            $connMix->query("delete from veiculo");
            $this->gravarVeiculos($req['GetVehiclesListResult']['Vehicle']);
            //Deleta a lista de veiculos antigas e slava a nova lista recuperada no WS---------------------------
            
            
            \Apoio\Helpers::msg("Integracao Mix", count($req['GetVehiclesListResult']['Vehicle'])." veiculos carregados");
        }else{
            $erro = new Erro("Veiculo", "aualizarListaVeiculo", "Conta não encontrada", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
    }
    
    /**
     * Metodo utilizada para realizar a gravação dos veiculos no banco de dados.
     * @param array $listaVeiculos Lista de objetos "Veiculos" para serem gravados.
     * @return Boolean Retorna TRUE se a gravação tiver corrido sem problemas.
     */
    private function gravarVeiculos($listaVeiculos){
        //Constroi a base do sql para inserção no banco de dados-----------------
        $sql = "insert into veiculo(
        id, site_id, group_id, description, 
        registration_number, unit_type, vehicle_icon, 
        target_fuel_cosumption, target_hourly_consumption, default_driver_id, 
        last_update, distance_checked, last_odometer, 
        last_trip, last_engine_seconds, last_service_key, 
        next_service_km, service_km, remind_km, 
        remind_km2, next_service_date, service_months, 
        remind_weeks, remint_weeks2, next_service_seconds, 
        service_seconds, remind_seconds, remind_seconds2, 
        last_licence, next_licence, licence_interval, 
        licence_remind, licence_remind2, last_cor, 
        next_cor, cor_interval, cor_remind, 
        cor_remind2, ignore_late_download, remind_on_distance, 
        remind_on_duration, service_hours, licence, 
        cor, default_allow_driver_access, created, 
        active, active_reason, active_state) values";
        
        $sql2 = "insert ignore veiculo_ignicao(id_veiculo, ignicao) values";
        //Constroi a base do sql para inserção no banco de dados-----------------
        
        
        
        //Monta o resto da query baseada em todos os objetos mandados na lista---------------------------------------------------
        $i = 0;
        foreach ($listaVeiculos as $veiculo){
            $i++;
            if($i > 1){
                $sql .= ",(";
                $sql2 .= ",(";
            }else{
                $sql .= "(";
                $sql2 .= "(";
            }
            $sql .= "'". (isset($veiculo['ID'])?$veiculo['ID']:null)."', ";
            $sql .= "'". (isset($veiculo['SiteID'])?$veiculo['SiteID']:null)."', ";
            $sql .= "'". (isset($veiculo['GroupID'])?$veiculo['GroupID']:null)."', ";
            $sql .= "'". (isset($veiculo['Description'])?$veiculo['Description']:null)."', ";
            $sql .= "'". (isset($veiculo['RegistrationNumber'])?$veiculo['RegistrationNumber']:null)."', ";
            $sql .= "'". (isset($veiculo['UnitType'])?$veiculo['UnitType']:null)."', ";
            $sql .= "'". (isset($veiculo['VehicleIcon'])?$veiculo['VehicleIcon']:null)."', ";
            $sql .= "'". (isset($veiculo['TargetFuelConsumption'])?$veiculo['TargetFuelConsumption']:null)."', ";
            $sql .= "'". (isset($veiculo['TargetHourlyConsumption'])?$veiculo['TargetHourlyConsumption']:null)."', ";
            $sql .= "'". (isset($veiculo['DefaultDriverID'])?$veiculo['DefaultDriverID']:null)."', ";
            $sql .= "'". (isset($veiculo['LastUpdate'])?$veiculo['LastUpdate']:null)."', ";
            $sql .= "'". (isset($veiculo['DistanceChecked'])?$veiculo['DistanceChecked']:null)."', ";
            $sql .= "'". (isset($veiculo['LastOdometer'])?$veiculo['LastOdometer']:null)."', ";
            $sql .= "'". (isset($veiculo['LastTrip'])?$veiculo['LastTrip']:null)."', ";
            $sql .= "'". (isset($veiculo['LastEngineSeconds'])?$veiculo['LastEngineSeconds']:null)."', ";
            $sql .= "'". (isset($veiculo['LastServiceKey'])?$veiculo['LastServiceKey']:null)."', ";
            $sql .= "'". (isset($veiculo['NextServiceKm'])?$veiculo['NextServiceKm']:null)."', ";
            $sql .= "'". (isset($veiculo['ServiceKm'])?$veiculo['ServiceKm']:null)."', ";
            $sql .= "'". (isset($veiculo['RemindKm'])?$veiculo['RemindKm']:null)."', ";
            $sql .= "'". (isset($veiculo['RemindKm2'])?$veiculo['RemindKm2']:null)."', ";
            $sql .= "'". (isset($veiculo['NextServiceDate'])?$veiculo['NextServiceDate']:null)."', ";
            $sql .= "'". (isset($veiculo['ServiceMonths'])?$veiculo['ServiceMonths']:null)."', ";
            $sql .= "'". (isset($veiculo['RemindWeeks'])?$veiculo['RemindWeeks']:null)."', ";
            $sql .= "'". (isset($veiculo['RemindWeeks2'])?$veiculo['RemindWeeks2']:null)."', ";
            $sql .= "'". (isset($veiculo['NextServiceSeconds'])?$veiculo['NextServiceSeconds']:null)."', ";
            $sql .= "'". (isset($veiculo['ServiceSeconds'])?$veiculo['ServiceSeconds']:null)."', ";
            $sql .= "'". (isset($veiculo['RemindSeconds'])?$veiculo['RemindSeconds']:null)."', ";
            $sql .= "'". (isset($veiculo['RemindSeconds2'])?$veiculo['RemindSeconds2']:null)."', ";
            $sql .= "'". (isset($veiculo['LastLicense'])?$veiculo['LastLicense']:null)."', ";
            $sql .= "'". (isset($veiculo['NextLicense'])?$veiculo['NextLicense']:null)."', ";
            $sql .= "'". (isset($veiculo['LicenseInterval'])?$veiculo['LicenseInterval']:null)."', ";
            $sql .= "'". (isset($veiculo['LicenseRemind'])?$veiculo['LicenseRemind']:null)."', ";
            $sql .= "'". (isset($veiculo['LicenseRemind2'])?$veiculo['LicenseRemind2']:null)."', ";
            $sql .= "'". (isset($veiculo['LastCOR'])?$veiculo['LastCOR']:null)."', ";
            $sql .= "'". (isset($veiculo['NextCOR'])?$veiculo['NextCOR']:null)."', ";
            $sql .= "'". (isset($veiculo['CORInterval'])?$veiculo['CORInterval']:null)."', ";
            $sql .= "'". (isset($veiculo['CORRemind'])?$veiculo['CORRemind']:null)."', ";
            $sql .= "'". (isset($veiculo['CORRemind2'])?$veiculo['CORRemind2']:null)."', ";
            $sql .= "'". (isset($veiculo['IgnoreLateDownload'])?$veiculo['IgnoreLateDownload']:null)."', ";
            $sql .= "'". (isset($veiculo['RemindOnDistance'])?$veiculo['RemindOnDistance']:null)."', ";
            $sql .= "'". (isset($veiculo['RemindOnDuration'])?$veiculo['RemindOnDuration']:null)."', ";
            $sql .= "'". (isset($veiculo['ServiceHours'])?$veiculo['ServiceHours']:null)."', ";
            $sql .= "'". (isset($veiculo['License'])?$veiculo['License']:null)."', ";
            $sql .= "'". (isset($veiculo['COR'])?$veiculo['COR']:null)."', ";
            $sql .= "'". (isset($veiculo['DefaultAllowDriverAccess'])?$veiculo['DefaultAllowDriverAccess']:null)."', ";
            $sql .= "'". (isset($veiculo['Created'])?$veiculo['Created']:null)."', ";
            $sql .= "'". (isset($veiculo['Active'])?$veiculo['Active']:null)."', ";
            $sql .= "'". (isset($veiculo['ActiveReason'])?$veiculo['ActiveReason']:null)."', ";
            $sql .= "'". (isset($veiculo['ActiveState'])?$veiculo['ActiveState']:null)."'";
            
            $sql2 .= "'". (isset($veiculo['ID'])?$veiculo['ID']:null)."', ";
            $sql2 .= '1';
            
            $sql .= ")";
            $sql2 .= ")";
        }
        //Monta o resto da query baseada em todos os objetos mandados na lista---------------------------------------------------
        
        
        
        
        
        //executa a gravação--------------------------------------------------------------------------------------------------
        $conn = Conexoes::conectarMix();
        try{
            $conn->query($sql);
            $conn->query($sql2);
            return true;
        } catch (Exception $ex) {
            $erro = new Erro("Veiculo", "gravarVeiculos", "Erro na inserção do sql no banco de dados", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        //executa a gravação--------------------------------------------------------------------------------------------------
    }
    
}
