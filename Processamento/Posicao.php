<?php
namespace Processamento;

use Apoio\Conexoes;
use Apoio\Erro;

/**
 * Classe de model para gravação de posições no padrão.
 *
 * @author Anderson
 * @since 28/08/2017
 */
class Posicao {
    /**
     *
     * @var String  Id de autonumeração da tabela do banco de dados.
     */
    public $id = null;
    /**
     *
     * @var String Id sequencial de posições que vem do web service da Mix.
     */
    public $idSeqMensagem = null;
    /**
     *
     * @var String Data e hora em qu o evento ou posição ocorreu.
     */
    public $dataHoraEvento = null;
    /**
     *
     * @var String data e hora em que o pacote de dados foi garavado no banco de dados do SUI.
     */
    public $dataHoraPacote = null;
    /**
     *
     * @var String Id unico que identifica o rastreador.
     */
    public $idRastreador = null;
    /**
     *
     * @var String Placa ou identificação que vem da Mix.
     */
    public $placa = null;
    /**
     *
     * @var String Id unico de identificação do motorista.
     */
    public $idMotorista = null;
    /**
     *
     * @var String Nome do motorista
     */
    public $nomeMotorista = null;
    /**
     *
     * @var String Coordenadas de latitude para localização.
     */
    public $latitude = null;
    /**
     *
     * @var String Coordenadas de longitude para localização.
     */
    public $longitude = null;
    /**
     *
     * @var String Direção em graus que o veiculo está se deslocando.
     */
    public $direcao = null;
    /**
     *
     * @var String Identificação de ignição LIGADA(1) ou DESLIGADA(0).
     */
    public $ignicao = null;
    /**
     *
     * @var String Id unico que idenfica o evento passado.
     */
    public $idEvento = null;
    /**
     *
     * @var String Nome descritivo para o evento que foi passado na posição.
     */
    public $evento = null;
    /**
     *
     * @var String Id unico para identificação de macro.
     */
    public $idMacro = null;
    /**
     *
     * @var String Texto contendo o conteudo da macro.
     */
    public $mensagemMacro = null;
    /**
     *
     * @var String Id unico que identifica a mensagem recebida.
     */
    public $idMensagemLivre = null;
    /**
     *
     * @var String Texto livre(Sem macro) contendo o conteudo da mensagem.
     */
    public $mensagemLivre = null;
    /**
     *
     * @var String Velocidade na hora da geração da posição atual.
     */
    public $velocidade = null;
    /**
     *
     * @var String Medidor de odometro no momento da geração da posição.
     */
    public $odometro = null;
    /**
     *
     * @var String Referencia de localizacao da posicao.
     */
    public $localizacao = null;
    /**
     *
     * @var String tensao ou porcentagem da bateria do rastreador ou localizador.
     */
    public $tensaoBateria = null;
    /**
     *
     * @var String Nome da tecnologia(Exemplo: Mix).
     */
    public $tecnologia = null;
    
    
    /**
     * Metodo utilizado para grava no banco de dados a lista de posições que é fornecida nos parametros.
     * @param array $listaPosicoes Lista de posições.
     * @return boolean Retorna TRUE se tudo tiver corrido bem, ou FALSE caso tenha havido algum erro.
     */
    public function gravarPosicoes($listaPosicoes){
        
        //Controe a base do sql para a execução no banco de dados------------------------------
        $sql = "insert into pacote_posicao("
                . "id_seq_mensagem, data_hora_evento, data_hora_pacote, id_rastreador, "
                . "placa, id_motorista, nome_motorista, "
                . "latitude, longitude, direcao, "
                . "ignicao, id_evento, evento, id_macro, "
                . "mensagem_macro, id_mensagem_livre, mensagem_livre, "
                . "velocidade, odometro, tecnologia, localizacao) values";
        //Controe a base do sql para a execução no banco de dados------------------------------
        
        
        
        //Constroe os dados que serão unificados com a base do sql para execução no banco de dados---------
        $i = 0;
        foreach ($listaPosicoes as $posicao){
            $i++;
            if($i > 1){
                $sql .= ",(";
            }else{
                $sql .= "(";
            }
            
            $sql .= "'{$posicao->idSeqMensagem}', ";
            $sql .= "'{$posicao->dataHoraEvento}', ";
            $sql .= "'{$posicao->dataHoraPacote}', ";
            $sql .= "'{$posicao->idRastreador}', ";
            $sql .= "'{$posicao->placa}', ";
            $sql .= "'{$posicao->idMotorista}', ";
            $sql .= "'{$posicao->nomeMotorista}', ";
            $sql .= "'{$posicao->latitude}', ";
            $sql .= "'{$posicao->longitude}', ";
            $sql .= "'{$posicao->direcao}', ";
            $sql .= "'{$posicao->ignicao}', ";
            $sql .= "'{$posicao->idEvento}', ";
            $sql .= "'{$posicao->evento}', ";
            $sql .= "'{$posicao->idMacro}', ";
            $sql .= "'{$posicao->mensagemMacro}', ";
            $sql .= "'{$posicao->idMensagemLivre}', ";
            $sql .= "'{$posicao->mensagemLivre}', ";
            $sql .= "'{$posicao->velocidade}', ";
            $sql .= "'{$posicao->odometro}', ";
            $sql .= "'{$posicao->tecnologia}', ";
            $sql .= "'{$posicao->localizacao}'";
            
            $sql .= ")";
            
        }
        //Constroe os dados que serão unificados com a base do sql para execução no banco de dados---------
        
        
        
        //Executa a sql e gera um erro caso algo saia errado----------------------------------------------------------------------
        try{
            $conn = Conexoes::conectarCentral();
            $conn->query($sql);
            return TRUE;
        } catch (Exception $ex) {
            $erro = new Erro("Posicao", "gravarPosicoes", "Erro de querry ao inserir no banco de dados", date("Y-m-d H:i:s"));
            $erro->registrarErro();
            return false;
        }
        //Executa a sql e gera um erro caso algo saia errado----------------------------------------------------------------------
        
    }
}
