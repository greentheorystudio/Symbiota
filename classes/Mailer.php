<?php
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    public function sendEmail($emailAddr,$subject,$bodyStr): ?string
    {
        $returnStr = '';
        if($GLOBALS['EMAIL_CONFIGURED']){
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $GLOBALS['SMTP_HOST'];
            if(isset($GLOBALS['SMTP_USERNAME'], $GLOBALS['SMTP_PASSWORD'])){
                $mail->SMTPAuth = true;
                $mail->Username = $GLOBALS['SMTP_USERNAME'];
                $mail->Password = $GLOBALS['SMTP_PASSWORD'];
            }
            if($GLOBALS['SMTP_ENCRYPTION']){
                if($GLOBALS['SMTP_ENCRYPTION_MECHANISM'] === 'STARTTLS'){
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }
                if($GLOBALS['SMTP_ENCRYPTION_MECHANISM'] === 'SMTPS'){
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                }
            }
            $mail->Port = $GLOBALS['SMTP_PORT'];

            $mail->setFrom($GLOBALS['PORTAL_EMAIL_ADDRESS'], $GLOBALS['DEFAULT_TITLE']);
            $mail->addAddress($emailAddr);

            $mail->isHTML();
            $mail->Subject = $subject;
            $mail->Body = $bodyStr;
            $mail->AltBody = strip_tags($bodyStr);
            try {
                $mail->send();
                $returnStr = 'Sent';
            } catch (Exception $e) {
                $returnStr = "Mailer Error: {$mail->ErrorInfo}";
            }
        }
        else{
            $returnStr = 'Error: Email not configured';
        }
        return $returnStr;
    }
}
