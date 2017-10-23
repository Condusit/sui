<?php 
namespace Entrada\Mix;
use SoapClient;
use SoapHeader;
use SoapFault;
use Exception;
/**
 * Classe que realiza requisições ao webservice da mix de acordo com parametros.
 * @version 1
 * @author Anderson
 */
class Requisitor {
    
    /**
     * 
     * @param array $arrayDados Array de dados no formato do xml de requisição do SOAP.
     * @param string $operacao Nome da operação que será requisitada no web service.
     * @param string $url URL da requisicao do web service.
     * @param array $arrayCabecalho Array com dados do header darequisição, este parametro é opcional.
     * @return array Retorn um array de Dados como resultado da requisição;
     * @throws  Gera uma possivel exceção na requisição do web service.
     */
    public static function enviaRequisicao($arrayDados,$operacao, $url, $dadosHeader = null, $ns = null){
        
        try{
            $cliente = new SoapClient($url);
            if($dadosHeader != null && $ns != null){
                $header = new SoapHeader($ns, "TokenHeader", $dadosHeader);
                $cliente->__setSoapHeaders($header);
            }
            $res = $cliente->__soapCall($operacao, $arrayDados);
        } catch (Exception $ex) {
            return new Exception($ex->getMessage());
        } catch (SoapFault $sf){
            return new Exception($sf->getMessage());
        }
        
        $json = json_encode($res);
        $resArray = json_decode($json, true);
        return $resArray;
    }
}
