<?php
namespace Entrada\Tracker;

use Apoio\Conexoes;
use Apoio\Erro;
use Entrada\Tracker\Requisitor;

/**
 * Classe que controla a atualização do token para as requisições
 *
 * @author Anderson
 * @since 11/09/2017
 */
class Login {
    public function buscaToken($usuario){
        $connTracker = Conexoes::conectarTracker();
        $dadosUsuario = $connTracker->query("select * from login where login.usuario = '{$usuario}'")->fetchAll();
        
        if(count($dadosUsuario) > 0){
            if($dadosUsuario[0]['dataExpiracao'] < date("Y-m-d H:i:s")){
                $req = new Requisitor();
                $cabecalho = array('Content-Type: application/json');
                $login_dados		= array(
		'username'		=> 'integracao.krona',
		'password'		=> '2017',
		'appid'			=> 168,
		'token'			=> NULL,
		'expiration'	=> 0
                );
                $corpo = json_encode($login_dados);
                $reqResult = $req->fazerRequisicao("seguranca/logon", $cabecalho, $corpo);
                if($reqResult['responseMessage'] == "logon.ok"){
                    $dataExpiracao = date("Y-m-d H:i:s", $reqResult['object']['expiration']);
                    $connTracker->query("update login set 
                                        login.token = '{$reqResult['object']['token']}', 
                                        login.dataExpiracao = '{$dataExpiracao}' 
                                        where 
                                        login.usuario = '{$reqResult['object']['username']}'");
                    return $reqResult['object']['token'];
                }
            }else{
                return $dadosUsuario[0]['token'];
            }
        }else{
            $erro = new Erro("Login", "buscaToken", "Usuario informado não foi localizado no banco de dados.", date("Y-m-d H:i:s"));
            $erro->registrarErro();
        }
    }
    
    private function gravaToken($token){
        
    }
}
