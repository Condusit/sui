<?php
namespace Entrada\Tracker;

use Apoio\Erro;

/**
 * Classe responsavel pelas requisições ao web service
 *
 * @author Anderson
 * @since 11/09/2017
 */
class Requisitor {
    public function fazerRequisicao($urlRequisicao, $cabecalho, $corpo){
        
        $curl = curl_init("http://integracao.grupotracker.com.br/".$urlRequisicao);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $cabecalho);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $corpo);
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $result	= curl_exec($curl);
	
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if ($status != 200) {
            $erro = new Erro("Requisitor", "fazerRequisicao", "Erro ao fazer requisicao no web service", date("Y-m-d H:i:s"));
            $erro->registrarErro();
            return;
	}
		
	curl_close($curl);
        return json_decode($result, true);
    }
}
