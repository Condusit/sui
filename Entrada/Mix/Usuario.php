<?php
namespace Entrada\Mix;

use Entrada\Mix\Requisitor;
use Apoio\Conexoes;
use Apoio\Erro;
/**
 * Classe responssavel por realizar operações de login e atualização de token
 * para as demais requisições.
 *
 * @author Anderson
 * @since 23/08/2017
 */
class Usuario {
    /**
     * 
     * @var String Nome do usuario.
     */
    public $usuario = null;
    /**
     * 
     * @var String Senha do usuario.
     */
    public $senha = null;
    /**
     *
     * @var String Token retornado para requisições futuras .
     */
    public $token = null;
    /**
     *
     * @var String Data do ultimo login.
     */
    public $ultimaDataLogin = null;
    
    
    
    
    
    
    
    /**
     * Metodo que realiza função de login em web service para conseguir 
     * um token valido que será utilizado na próximas requisições.
     * @param String $usuario Nome do usuario que precisa requisitar um novo token no sistema.
     * @return String $token Codigo de token que será utilizado para requisições.
     * @throws Erro de login Execassão em falha de usuario ou senha.
     */
    public function fazerLogin($usuario){

        //Chama conexão com banco "mix" e recupera os dados do usuario------------------------------------------------
        $conn = Conexoes::conectarMix();
        $usuarioBanco = $conn->query("select * from mix.usuario usu where usu.usuario = '{$usuario}'")->fetchAll();
        //Chama conexão com banco "mix" e recupera os dados do usuario------------------------------------------------
        
        if(count($usuarioBanco) > 0){
            //Prepara os dados para realizar a requisição ao web service--------------------
            $url = "https://new.api.fm-web.us/webservices/CoreWebSvc/CoreWS.asmx?WSDL";
            $operacao = "Login";
            $arrayDados = array(
                'Login' => array(
                    'UserName' => $usuarioBanco[0]['usuario'],
                    'Password' => $usuarioBanco[0]['senha'],
                    'ApplicationID' => '0'
                    )
                );
            //Prepara os dados para realizar a requisição ao web service--------------------



            
            $req = new Requisitor();
            $res = $req->enviaRequisicao($arrayDados, $operacao, $url);

            echo "";
            
            if($res['LoginResult']['CoreLoginResult']['Indicator'] == "Success"){
                $sql = "update usuario set 
                usuario.token = '{$res['LoginResult']['Token']}' 
                where 
                usuario.usuario = '{$usuarioBanco[0]['usuario']}'";
                $conn->query($sql);
                return $res['LoginResult']['Token'];
            }else{
                $erro = new Erro("Usuario", "fazerlogin()", "Erro na requisição do web service", date("Y-m-d H:i:s"));
                $erro->registrarErro();
            }
            
        }
        
        
    }       
}