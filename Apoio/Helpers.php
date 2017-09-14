<?php
namespace Apoio;


/**
 * Classe estatica com funções de apoio ao sistema.
 *
 * @author Anderson
 */
class Helpers {
    
    /**
     * Metodo para conversão de xml em array.
     * @version 1
     * @param String $xml String com xml a ser convertido.
     * @return Array
     * Retorna um array com dados contidos no xml da acordo com a hierarquia
     * mesmo.
     */
    public static function xml_to_array($xml){
    
        $xml = simplexml_load_string($xml);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);

        return $array;
    }
    
    public static function msg($programa, $mensagem){
        echo "\n[{$programa}][".date("Y-m-d H:i:s")."] - {$mensagem}";
    }
    
    public static function limpaDados($texto){
       return str_replace(array("'", "--"), array(" ", "-"),$texto);
    }
}
