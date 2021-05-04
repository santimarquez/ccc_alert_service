<?php

class MailNotification
{
    private $mail;
    private $resources_required = true;
    private $view_path = __DIR__ . '/view/';
    private $imports_path = __DIR__ . '/import/';
    private $layouts_path = __DIR__ . '/view/layout/';
    private $footer_layout;
    private $header_layout;

    /**
     * Creates a new instance of PHPMailer
     * using the settings provided within the
     * .env file.
     */
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
            $this->mail->CharSet  = 'UTF-8';
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
    }

    /**
     * Set the address for the email.
     * The name is not required
     *
     * @param string $email_address
     * @param string $name
     * @return boolean
     */
    public function setAddress($email_address, $name = NULL)
    {
        try {
            if ($name) {
                $this->mail->addAddress($email_address, $name);
            } else {
                $this->mail->addAddress($email_address);
            }
        } catch (Exception $e) {
            Log::add("Error adding address: " . $this->mail->ErrorInfo);
            $this->setError();
            return false;
        }

        return true;
    }

    /**
     * Add attachment to the email.
     * It case that it receives an array
     * with different files, it will iterate
     * and add each file.
     * 
     * Will throw an error in case that it finds an error 
     * adding one of the files.
     *
     * @param string|array $data
     * @return boolean
     */
    public function addAttachment($data)
    {
        if (gettype($data) == 'array') {
            foreach ($data as $file) {
                try {
                    $this->mail->addAttachment($file);
                } catch (Exception $e) {
                    Log::add("Error adding attachment: " . $this->mail->ErrorInfo);
                    $this->setError();
                    return false;
                }
            }

            return true;
        }

        try {
            $this->mail->addAttachment($data);
        } catch (Exception $e) {
            Log::add("Error adding attachment: " . $this->mail->ErrorInfo);
            $this->setError();
            return false;
        }

        return true;
    }

    /**
     * Send the email
     *
     * @return void
     */
    public function send()
    {
        try {
            $this->mail->send();
        } catch (Exception $e) {
            Log::add("Error sending email: " . $this->mail->ErrorInfo);
            $this->setError();
            return false;
        }

        return true;
    }

    /**
     * Load the html view created for the selected
     * notification type. The view are stored on
     * the directory specified in $view_path
     *
     * @param string $notification_name
     * @return boolean
     */
    public function loadView($notification_name)
    {
        try {
            $notification_name .= '.html';
            $this->mail->Body = file_get_contents($this->layouts_path . $this->header_layout . '-header.html');
            $this->mail->Body .= file_get_contents($this->view_path . $notification_name);
            $this->mail->Body .= file_get_contents($this->layouts_path . $this->footer_layout . '-footer.html');
        } catch (Exception $e) {
            Log::add("Error including the view: " . $this->mail->ErrorInfo);
            $this->setError();
            return false;
        }

        return true;
    }

    /**
     * Load the backend data necessary to send a notification
     * It will get it from the file located in the path $imports_path
     * 
     *
     * @param string $notification_name
     * @param array $resources
     * @return boolean
     */
    public function importBackend($notification_name, $resources = NULL)
    {
        try {
            include $this->imports_path . $notification_name . '.php';
        } catch (Exception $e) {
            Log::add("Error importing the backend: " . $this->imports_path . $notification_name . '.php');
            $this->setError();
            return false;
        }

        return true;
    }
}
