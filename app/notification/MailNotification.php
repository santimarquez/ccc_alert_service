<?php

class MailNotification
{
    private $mail;
    private $error_flag;

    function __construct()
    {
        try {
            $this->mail = new PHPMailer(true);
            $this->mail->isSMTP();
            $this->mail->Host = $_ENV["SMTP_CLIENT"];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $_ENV["SMTP_USERNAME"];
            $this->mail->Password = $_ENV["SMTP_PASSWORD"];
            $this->mail->SMTPSecure = 'tls';
            $this->mail->Port = $_ENV["SMTP_PORT"];
            $this->mail->setFrom($_ENV["NO-REPLY_MAIL"], $_ENV["NO-REPLY_NAME"]);
            $this->mail->isHTML(true);
        } catch (Exception $e) {
            Log::add("Message structure cannot be created. Mailer Error: " . $this->mail->ErrorInfo);
        }
    }

    public function setError()
    {
        $this->error_flag = true;
        $this->mail->Body = 'New critical error has been detected</b>';
        $this->mail->Subject = 'Error detected on CCC Service';
    }

    public function setAddress($email_address, $name = NULL)
    {
        if ($name) {
            $this->mail->addAddress($email_address, $name);
        } else {
            $this->mail->addAddress($email_address);
        }
    }

    public function addAttachment($file)
    {
        try {
            $this->mail->addAttachment($file);
        } catch (Exception $e) {
            Log::add("Error adding attachment: " . $this->mail->ErrorInfo);
        }
    }

    public function send()
    {
        try {
            $this->mail->send();
        } catch (Exception $e) {
            Log::add("Error sending email: " . $this->mail->ErrorInfo);
        }
    }
}
