<?php

class MailNotification
{
    private $smtp_client;
    private $error_flag;

    function __construct()
    {
        $this->smtp_client = $_ENV["SMTP_CLIENT"];
    }

    public function setError()
    {
        $this->error_flag = true;
    }
}
