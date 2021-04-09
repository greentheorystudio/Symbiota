<?php
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    public function sendEmail($emailAddr,$subject,$bodyStr)
    {
        $mail = new PHPMailer(true);
        try {
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

            $mail->setFrom($GLOBALS['ADMIN_EMAIL'], $GLOBALS['DEFAULT_TITLE'] . ' Admin');
            $mail->addAddress($emailAddr);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $bodyStr;
            $mail->AltBody = strip_tags($bodyStr);

            $mail->send();
            return 'Sent';
        } catch (Exception $e) {
            return "Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
