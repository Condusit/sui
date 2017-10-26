<?php
namespace Apoio;

use Apoio\Mail;
use Apoio\Conexoes;

/**
 * Classe destinada a tratamento de erros no sistema.
 *
 * @author Anderson
 * @version 1
 * 
 */
class Erro {
    /**
     *
     * @var String nome da classe ou arquivo onde ocorreu o erro.
     */
    public $classe = null;
    /**
     *
     * @var String Nome do metodo ou função onde ocorreu o erro.
     */
    public $metodo = null;
    /**
     *
     * @var String Descrição breve do erro.
     */
    public $descricao = null;
    /**
     *
     * @var String Data do momento em que ocorreu o erro.
     */
    public $dataHora = null;
    
    /**
     * Construtor da classe de erro.
     * @param String $classe Classe onde aconteceu o erro.
     * @param String $metodo Metodo onde aconteceu o erro.
     * @param String $descricao Descrição detalhada do erro.
     * @param String $datahora Data e hora quando aconteceu o erro.
     */
    public function __construct($classe, $metodo, $descricao, $datahora) {
        $this->classe = $classe;
        $this->dataHora = $datahora;
        $this->descricao = $descricao;
        $this->metodo = $metodo;
    }
    
    /**
     * Metodo que server para registrar o erro gerado com os dado
     * informados nas propriedades.
     * @return boolean Retorna "TRUE" se o email foi enviado com sucesso.
     */
    public function registrarErro(){
        if($this->dataHora != null){
            if($this->classe != null){
                if($this->metodo != null){
                    if(count($this->descricao != null)){
                        //Registra o erro em arquivos de log .TXT----------
                        $linha = "\n========================================================\n";
                        $linha .= "Data: ".$this->dataHora."\n";
                        $linha .= "Erro: ".$this->descricao."\n";
                        $linha .= "Classe: {$this->classe}\n";
                        $linha .= "Metodo: {$this->metodo}\n";
                        $linha .= "========================================================\n";
                        $nomeArquivo =  "C:\\xampp\\htdocs\\sui\\logs\\".date("Y-m-d");
                        $arquivo = fopen("{$nomeArquivo}.txt", "a");
                        fwrite($arquivo, $linha);
                        fclose($arquivo);
                        Helpers::msg("Erro", "Houve um erro no processo verifique o log para mais informacoes");
                        //Registra o erro em arquivos de log .TXT----------


                        //Envia email para todos os programadores---------------------------
//                        $conn = Conexoes::conectarCentral();
//                        $programadores = $conn->query("select programadores.email from programadores")->fetchAll();
//                        $enderecos = Array();
//                        if(count($programadores) > 0){
//                            foreach ($programadores as $programador){
//                                array_push($enderecos, $programador['email']);
//                            }
//                        }else{
//                            array_push($enderecos, "ti@kronamaxxi.com.br");
//                        }
//                        $email = new Mail();
//                        $email->de = "sui@kronamaxxi.com.br";
//                        $email->para = $enderecos;
//                        $email->titulo = "Erro no sistema de integrações";
//                        $email->corpo = $this->corpoEmail();
//                        try{
//                            $email->enviaEmail();
//                        } catch (Exception $ex) {
//                            echo $ex->getMensage();
//                        }
//                        return true;
                        //Envia email para todos os programadores---------------------------
                    }else{
                        echo "A descricao do erro nao foi informada";
                    }
                }else{
                    echo "Metodo do erro nao foi informado";
                }
            }else{
                echo "Classe do erro nao foi informada";
            }
        }else{
            echo "Date e hora do erro não informada";
        }
    }
    
    private function corpoEmail(){
        $corpo = '<html><meta charset="UTF-8">
		<meta property="og:image" content="http://187.50.239.83/integracao/background.png">
		<meta property="og:title" content="ano novo 2">
		<meta property="og:image:type" content="image/jpeg" />
		<meta property="og:image:width" content="180" />
		<meta property="og:image:height" content="110" />
		<link href="/email-marketing/style.css" rel="stylesheet" type="text/css" media="all" />

		<div class="conteudo" style="margin-top:0px;">
		<html>

   <style>body{margin: 0;padding: 0;}@media only screen and (max-width: 640px){table, img[class="partial-image"]{ width:100% !important; min-width: 200px !important; }}</style>
   <body topmargin=0 leftmargin=0>
      <!--?xml encoding="utf-8" ?-->
      <table style="border-collapse: collapse; border-spacing: 0; min-height: 418px;" cellpadding="0" cellspacing="0" width="100%" bgcolor="#f2f2f2">
         <tbody>
            <tr>
               <td align="center" style="border-collapse: collapse; padding-top: 30px; padding-bottom: 30px;">
                  <table cellpadding="5" cellspacing="5" width="600" bgcolor="white" style="border-collapse: collapse; border-spacing: 0;">
                     <tbody>
                        <tr>
                           <td style="border-collapse: collapse; padding: 0px; text-align: center; width: 600px;">
                              <table style="border-collapse: collapse; border-spacing: 0; box-sizing: border-box; min-height: 40px; position: relative; width: 100%; max-width: 600px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px; text-align: center;">
                                 <tbody>
                                    <tr>
                                       <td style="border-collapse: collapse; font-family: Arial; padding: 0px; line-height: 0px; mso-line-height-rule: exactly;">
                                          <table width="100%" style="border-collapse: collapse; border-spacing: 0; font-family: Arial;">
                                             <tbody>
                                                <tr>
                                                   <td align="center" style="border-collapse: collapse; line-height: 0px; padding: 0; mso-line-height-rule: exactly;"><a target="_blank" style="display: block; text-decoration: none; box-sizing: border-box; font-family: arial; width: 100%;"><img class="partial-image" src="http://187.50.239.83/integracao/background.png" width="600" style="box-sizing: border-box; display: block; max-width: 600px; min-width: 160px;"></a></td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                              <table style="border-collapse: collapse; border-spacing: 0; box-sizing: border-box; min-height: 40px; position: relative; width: 100%;">
                                 <tbody>
                                    <tr>
                                       <td style="border-collapse: collapse; font-family: Arial; padding: 10px 15px;">
                                          <table width="100%" style="border-collapse: collapse; border-spacing: 0; text-align: left; font-family: Arial;">
                                             <tbody>
                                                <tr>
                                                   <td style="border-collapse: collapse;">
                                                      <div style="font-family: Arial; font-size: 15px; font-weight: normal; line-height: 170%; text-align: left; color: #666; word-wrap: break-word;">
	                                                      <span>
	                                                      	<p>
	                                                      		<strong>Data Hora:</strong> '.$this->dataHora.'
	                                                      	</p>
	                                                      	<p>
																<strong>Classe:</strong> '.$this->classe.'
	                                                      	</p>
	                                                      	<p>
	                                                      		<strong>Metodo:</strong> '.$this->metodo.'
	                                                      	</p>
	                                                      	<p>
		                                                      	<strong>Descrição:</strong> '.$this->descricao.'
															</p>
	                                                      </span>
                                                      </div>
                                                   </td>
                                                </tr>
                                             </tbody>
                                          </table>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
   </body>
</html>';
        return $corpo;
    }
}
