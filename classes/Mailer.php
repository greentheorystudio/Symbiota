<?php
require __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    public function sendEmail($emailAddr,$subject,$bodyStr)
    {
        global $DEFAULT_TITLE, $ADMIN_EMAIL, $SMTP_HOST, $SMTP_PORT, $SMTP_ENCRYPTION, $SMTP_ENCRYPTION_MECHANISM, $SMTP_USERNAME, $SMTP_PASSWORD;
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = $SMTP_HOST;
            if(isset($SMTP_USERNAME, $SMTP_PASSWORD)){
                $mail->SMTPAuth = true;
                $mail->Username = $SMTP_USERNAME;
                $mail->Password = $SMTP_PASSWORD;
            }
            if($SMTP_ENCRYPTION){
                if($SMTP_ENCRYPTION_MECHANISM === 'STARTTLS'){
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }
                if($SMTP_ENCRYPTION_MECHANISM === 'SMTPS'){
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                }
            }
            $mail->Port = $SMTP_PORT;

            $mail->setFrom($ADMIN_EMAIL, $DEFAULT_TITLE . ' Admin');
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
