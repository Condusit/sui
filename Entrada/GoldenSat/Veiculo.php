<?php
namespace Entrada\Goldensat;

use Entrada\Goldensat\Requisicao;
use Apoio\Conexoes;

/**
 * Classe que controla o recebimento de veiculos do web service.
 *
 * @author Anderson
 * @since 06/09/2017
 */
class Veiculo {
    /**
     * Metodo utilizado para busca a lista de veiculos com a ultima posição do 
     * mesmo e realizar a gravação na tabela Veiculos do banco de dados Goldensat.
     */
    public function buscarVeiculos(){
        $connGoldensat = Conexoes::conectarGoldensat();
        
        $req = new Requisicao();
        $veiculos = $req->requisitar("http://mobile.stctecnologia.com.br/ws/veiculos.php");
        
        $i = 0;
        $sql = "replace into veiculos(codveic, placa, `data`, ignicao, odometro, horimetro, velocidade, s1, s2, tensao, latitude, longitude, endereco, tipoveic, rotulo, rpm, novo) values";
        foreach ($veiculos['veiculo'] as $veiculo){
            $i++;
            
            
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['codveic'])?"":$veiculo['codveic']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['placa'])?"":$veiculo['placa']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['data'])?"":$veiculo['data']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['ignicao'])?"":$veiculo['ignicao']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['odometro'])?"":$veiculo['odometro']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['horimetro'])?"":$veiculo['horimetro']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['velocidade'])?"":$veiculo['velocidade']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['s1'])?"":$veiculo['s1']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['s2'])?"":$veiculo['s2']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['tensao'])?"":$veiculo['tensao']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['latitude'])?"":$veiculo['latitude']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['longitude'])?"":$veiculo['longitude']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['endereco'])?"":$veiculo['endereco']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['tipoveic'])?"":$veiculo['tipoveic']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['rotulo'])?"":$veiculo['rotulo']))."', ";
            $sql .= "'".\Apoio\Helpers::limpaDados((is_array($veiculo['rpm'])?"":$veiculo['rpm']))."', ";
            $sql .= "'1'";
            
            $sql .= ")";
        }
        
        $connGoldensat->query($sql);
        \Apoio\Helpers::msg("Integracao Goldensat", count($veiculos['veiculo'])." veiculos atualizados");
    }
}
