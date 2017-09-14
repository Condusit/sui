<?php
namespace Apoio;


/**
 * Classe para envio de email
 *
 * @author Anderson
 * @since 22/08/2017
 */
class Mail {
    /**
     * 
     * @var String Endereço de email de onde está vindo o email.
     */
    public $de = null;
    /**
     *
     * @var Array Lista de endereços de emails para quem a mensagem será enviada.
     */
    public $para = Array();
    /**
     *
     * @var String Titulo do email a ser enviado.
     */
    public $titulo = null;
    /**
     *
     * @var String corpo da mesagem que poderá ser texto comum ou HTML.
     */
    public $corpo = null;
    
    /**
     * Metodo utilizado para enviar os email depois de definidos os dados na prorpiedade.
     * @return boolean Retorna "True" se o email tiver sido enviado.
     * @throws Exception Retorna exceptions caso ocorra um erro no envio de email.
     */
    public function enviaEmail(){
        if($this->de != null){
            if($this->titulo != null){
                if(count($this->para) > 0){
                    if($this->corpo != null){
                        $mail = new PHPMailer();
                        $mail->IsSMTP();
                        $mail->SMTPAuth = true;
                        $mail->SMTPKeepAlive = true;
                        $mail->Port = 587;
                        $mail->Host = "smtplw.com.br";
                        $mail->Username = "grupokrona";
                        $mail->Password = "Krona001";
                        $mail->SetFrom($this->de);
                        for($i = 0; $i < count($this->para); $i++){
                            if($i == 0){
                                $mail->AddAddress($this->para[$i]);
                            }else{
                                $mail->AddReplyTo($this->para[$i]);
                            }
                        }
                        $mail->CharSet = "UTF-8";
                        $mail->IsHTML(true);
                        $mail->Subject = $this->titulo;
                        $mail->Body = $this->corpo;
                        
                        if(!$mail->Send()){
                            throw new Exception("Erro ao enviar email");
                        }else{
                            return true;
                        }
                    }else{
                        echo "não foi informado corpo do texto";
                    }
                }else{
                    echo "não há nenhum endereço de email informado para envio";
                }
            }else{
                echo "O campo tituo não pode ficar em branco";
            }
        }else{
            echo "O campo 'de' não pode ficar em branco";
        }
    }
    
}
