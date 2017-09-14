<?php
namespace Entrada\Mix;

use Apoio\Conexoes;
/**
 * Classe responsavel por manter os dadoas de motorista atualizados.
 *
 * @author Anderson
 * @since 24/08/2017
 */
class Motorista {
    public $ID = null;
    public $SiteID = null;
    public $GroupID = null;
    public $EmployeeNumber = null;
    public $ExtendedID = null;
    public $Name = null;
    public $DefaultAllowVehicleAccess = null;
    public $License = null;
    public $LastLicense = null;
    public $NextLicense = null;
    public $LicenseInterval = null;
    public $LicenseRemind = null;
    public $LicenseRemind2 = null;
    public $DistanceChecked = null;
    public $Created = null;
    public $Updated = null;
    
    /**
     * Metodo que limpa e reinsere dados na tabela "Motorista" do banco de dados.
     */
    public function atualizarListaMotorista(){
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        $connMix = Conexoes::conectarMix();
        $usuario = $connMix->query("select usuario.token from usuario where usuario.usuario = 'krona'")->fetchAll();
        //Conecta e busca o token para realização da requisição----------------------------------------------------------
        
        
        
        if(count($usuario) > 0){
            //Monta os dados que são necessários para requisição--------------------------------------------
            $ns = "http://www.omnibridge.com/SDKWebServices/AssetData";
            $header = array("Token" => $usuario[0]['token']);
            $body = array("GetDriverList" => "");
            $url = "https://new.api.fm-web.us/webservices/AssetDataWebSvc/DriverProcessesWS.asmx?WSDL";
            //Monta os dados que são necessários para requisição--------------------------------------------
            
            
            
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            try{
                $req = Requisitor::enviaRequisicao($body, "GetDriverList", $url, $header, $ns);
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
                    $this->atualizarListaMotorista();
                }else{
                    $erro = new Erro("Veiculo", "atualizarListaVeiculos", "Erro ao realizar requisição do web service", date("Y-m-d H:i:s"));
                    $erro->registrarErro();
                }
                //Se retornar que token é invalido faz o login para obter um novo token--------------------------------------------------------------------------------------------------------------------
            }
            //Executa a requisição-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
            
            
            //Deleta a lista de motorista e e atualiza a mesma----------------
            $connMix->query("delete from motorista");
            $this->gravarMotoristas($req['GetDriverListResult']['Driver']);
            //Deleta a lista de motorista e e atualiza a mesma----------------
            
            
            \Apoio\Helpers::msg("Integracao Mix", count($req['GetDriverListResult']['Driver'])." motoristas carregados");
        }else{
            $erro = new Erro("Veiculo", "aualizarListaVeiculo", "Conta não encontrada", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
    }
    
    
    /**
     * Metodo utilizado para gravar a lista de motoristas no banco de dados.
     * @param array $listaMotoristas
     */
    private function gravarMotoristas($listaMotoristas){
        
        //Prepara a sql para execução-------------------------------
        $sql = "insert into motorista("
        . "id, site_id, group_id, "
        . "employee_number, extend_id, name, "
        . "defauklt_allow_vehicle_access, "
        . "licence, last_licence, next_licence, "
        . "licence_interval, licence_remind, licence_remind2, "
        . "distance_checked, created, updated) values";
        //Prepara a sql para execução-------------------------------
        
        
        //Coloca os dados para execução do sql--------------------------------------------------------------- 
        $i = 0;
        foreach ($listaMotoristas as $motorista){
            $i++;
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            $sql .= "'". (isset($motorista['ID'])?$motorista['ID']:null)."', ";
            $sql .= "'". (isset($motorista['SiteID'])?$motorista['SiteID']:null)."', ";
            $sql .= "'". (isset($motorista['GroupID'])?$motorista['GroupID']:null)."', ";
            $sql .= "'". (isset($motorista['EmployeeNumber'])?$motorista['EmployeeNumber']:null)."', ";
            $sql .= "'". (isset($motorista['ExtendedID'])?$motorista['ExtendedID']:null)."', ";
            $sql .= "'". (isset($motorista['Name'])?$motorista['Name']:null)."', ";
            $sql .= "'". (isset($motorista['DefaultAllowVehicleAccess'])?$motorista['DefaultAllowVehicleAccess']:null)."', ";
            $sql .= "'". (isset($motorista['License'])?$motorista['License']:null)."', ";
            $sql .= "'". (isset($motorista['LastLicense'])?$motorista['LastLicense']:null)."', ";
            $sql .= "'". (isset($motorista['NextLicense'])?$motorista['NextLicense']:null)."', ";
            $sql .= "'". (isset($motorista['LicenseInterval'])?$motorista['LicenseInterval']:null)."', ";
            $sql .= "'". (isset($motorista['LicenseRemind'])?$motorista['LicenseRemind']:null)."', ";
            $sql .= "'". (isset($motorista['LicenseRemind2'])?$motorista['LicenseRemind2']:null)."', ";
            $sql .= "'". (isset($motorista['DistanceChecked'])?$motorista['DistanceChecked']:null)."', ";
            $sql .= "'". (isset($motorista['Created'])?$motorista['Created']:null)."', ";
            $sql .= "'". (isset($motorista['Updated'])?$motorista['Updated']:null)."'";
            
            $sql .= ")";
        }
        //Coloca os dados para execução do sql---------------------------------------------------------------
        
        
        
        
        $conn = Conexoes::conectarMix();
        try{
            $conn->query($sql);
        } catch (Exception $ex) {
            $erro = new Erro("Motorista", "gravarMotoristas", "Erro na inserção do sql no banco de dados", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
        
    }
}
