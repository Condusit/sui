<?php
namespace Entrada\Tracker;

use Apoio\Conexoes;
use Entrada\Tracker\Login;
use Entrada\Tracker\Requisitor;

/**
 * classe que controla a obtenção de motoristas.
 *
 * @author Anderson
 * @since 12/09/2017
 */
class Motorista {
    public function buscaMotoristas(){
        $connTracker = Conexoes::conectarTracker();
        
        $login = new Login();
        $token = $login->buscaToken("integracao.krona");
        
        $req = new Requisitor();
        $cabecalho = array('Content-Type: application/json', 'token: '.$token);
        $corpo = null;
        $reqResposta = $req->fazerRequisicao("motoristas", $cabecalho, $corpo);
        
        echo "fim";
    }
}
