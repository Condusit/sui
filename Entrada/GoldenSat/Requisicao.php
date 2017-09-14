<?php
namespace Entrada\Goldensat;

/**
 * Classe que realiza a requição no web service.
 *
 * @author Anderson
 * @since 06/09/2017
 */
class Requisicao {
    
    /**
     * Metodo de requisição generica do web service
     * @param string $url URL base para o tipo de requisição que será soplicitada.
     * @return Array Retorna uma lista de elementos seja ela Veiculos ou eventos.
     */
    public function requisitar($url){
        $usuario = md5("krona-isca");
        $senha = md5("123456");
        
        $url = $url."?s={$usuario}&d={$senha}&y=gds";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_URL, $url);
        $result = curl_exec($curl);
        $lista = \Apoio\Helpers::xml_to_array($result);
        return $lista;
    }
}
