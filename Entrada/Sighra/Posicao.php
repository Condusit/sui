<?php
namespace Entrada\Sighra;

use Apoio\Conexoes;

/**
 * Classe que controla as posições que são obtidas.
 *
 * @author Anderson
 * @since 15/09/2017
 */
class Posicao {
    public function buscarPosicoes(){
        $connSighra = Conexoes::conectarSighra();
        $connServidorSighra = Conexoes::conectarServidorSighra();
        
        $referencia = $connSighra->query("select * from controles where controles.id = 'posicoes'")->fetchAll();
        $ref = explode(",", $referencia[0]['posicoes']);
        
        $posicoes = $connServidorSighra->query("select * from log_historico_{$ref[0]} where lhis_id > '{$ref[1]}'")->fetchAll();
        
        
    }
}
