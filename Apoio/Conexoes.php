<?php
namespace Apoio;

use PDO;

/**
 * Classe estatica que server para conectar com os varios 
 * bancos de dados diferentes usados na aplicação.
 *
 * @author Anderson
 * @since 22/08/2017
 */
class Conexoes {
    
    /**
     * Metodo para conectar com o banco de dados "mix".
     * @return PDO 
     * Retorna um objeto "PDO" com a conexão do banco de dados "mix".
     * Ocorra um erro con a conexão retorna null.
     * @throws Exception
     * Gera erros de conexão com mysql.
     */
    public static function conectarMix(){
        $dns = 'mysql:host=127.0.0.1;dbname=mix';
        $user = 'integrador';
        $password = 'etltecno';
        try{
            $conn = new \PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "360"));
            return $conn;
        }catch(Execelption $ex){
            throw $ex;
        }
    }
    
    /**
     * Metodo para conectar com o banco de dados "Central".
     * @return PDO 
     * Retorna um objeto "PDO" com a conexão do banco de dados "central".
     * Ocorra um erro con a conexão retorna null.
     * @throws Exception
     * Gera erros de conexão com mysql.
     */
    public static function conectarCentral(){
        $dns = 'mysql:host=127.0.0.1;dbname=central_integracoes';
        $user = 'integrador';
        $password = 'etltecno';
        try{
            $conn = new \PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "360"));
            return $conn;
        }catch(Execelption $ex){
            throw $ex;
        }
    }
    
    /**
     * Metodo para conectar com o banco de dados do servidro 170 no banco "Kronaone".
     * @return PDO 
     * Retorna um objeto "PDO" com a conexão do banco de dados "Kronaone".
     * Ocorra um erro con a conexão retorna null.
     * @throws Exception
     * Gera erros de conexão com mysql.
     */
    public static function conectarK1Kronaone(){
        $dns = 'mysql:host=192.168.1.170;dbname=kronaone';
        $user = 'integrador';
        $password = 'etltecno';
        try{
            $conn = new \PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "360"));
            return $conn;
        }catch(Execelption $ex){
            throw $ex;
        }
    }
    
    /**
     * Metodo para conectar com o banco de dados do servidro 170 no banco "Integrador".
     * @return PDO 
     * Retorna um objeto "PDO" com a conexão do banco de dados "integrador".
     * Ocorra um erro con a conexão retorna null.
     * @throws Exception
     * Gera erros de conexão com mysql.
     */
    public static function conectarK1Integrador(){
        $dns = 'mysql:host=192.168.1.170;dbname=integrador';
        $user = 'integrador';
        $password = 'etltecno';
        try{
            $conn = new \PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "360"));
            return $conn;
        }catch(Execelption $ex){
            throw $ex;
        }
    }
    
    /**
     * Metodo para conectar com o banco de dados do Controle de jornada.
     * @return PDO 
     * Retorna um objeto "PDO" com a conexão do banco de dados "cj_homologacao".
     * Ocorra um erro con a conexão retorna null.
     * @throws Exception
     * Gera erros de conexão com mysql.
     */
    public static function conectarControleJornada(){
        $dns = 'mysql:host=192.168.1.46;dbname=cj_homologacao';
        $user = 'sui';
        $password = 'sui@krona';
        try{
            $conn = new \PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "360"));
            return $conn;
        }catch(Execelption $ex){
            throw $ex;
        }
    }
    
    /**
     * Metodo para conectar com o banco de dados do servidro 176 do "Servidor da Sighra".
     * @return PDO 
     * Retorna um objeto "PDO" com a conexão do banco de dados "mclient".
     * Ocorra um erro con a conexão retorna null.
     * @throws Exception
     * Gera erros de conexão com mysql.
     */
    public static function conectarServidorSighra(){
        $dns = 'mysql:host=192.168.1.176;dbname=mclient';
        $user = 'integrador';
        $password = 'etltecno';
        try{
            $conn = new \PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "360"));
            return $conn;
        }catch(Execelption $ex){
            throw $ex;
        }
    }
    
    
    /**
     * Metodo para conectar com o banco de dados do "Sighra".
     * @return PDO 
     * Retorna um objeto "PDO" com a conexão do banco de dados "sighra".
     * Ocorra um erro con a conexão retorna null.
     * @throws Exception
     * Gera erros de conexão com mysql.
     */
    public static function conectarSighra(){
        $dns = 'mysql:host=127.0.0.1;dbname=sighra';
        $user = 'integrador';
        $password = 'etltecno';
        try{
            $conn = new \PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "360"));
            return $conn;
        }catch(Execelption $ex){
            throw $ex;
        }
    }
    
    /**
     * Metodo para conectar com o banco de dados do "GoldenSat".
     * @return PDO 
     * Retorna um objeto "PDO" com a conexão do banco de dados "goldensat".
     * Ocorra um erro con a conexão retorna null.
     * @throws Exception
     * Gera erros de conexão com mysql.
     */
    public static function conectarGoldensat(){
        $dns = 'mysql:host=127.0.0.1;dbname=goldensat';
        $user = 'integrador';
        $password = 'etltecno';
        try{
            $conn = new \PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "360"));
            return $conn;
        }catch(Execelption $ex){
            throw $ex;
        }
    }
    
    /**
     * Metodo para conectar com o banco de dados do "Tracker".
     * @return PDO 
     * Retorna um objeto "PDO" com a conexão do banco de dados "tracker".
     * Ocorra um erro con a conexão retorna null.
     * @throws Exception
     * Gera erros de conexão com mysql.
     */
    public static function conectarTracker(){
        $dns = 'mysql:host=127.0.0.1;dbname=tracker';
        $user = 'integrador';
        $password = 'etltecno';
        try{
            $conn = new \PDO($dns, $user, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_TIMEOUT => "360"));
            return $conn;
        }catch(Execelption $ex){
            throw $ex;
        }
    }
}
