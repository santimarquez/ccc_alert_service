<?php

class Notification
{
    public $notification;

    function __construct($notification_type = 'mail', $error = NULL)
    {
        $this->notification = ($notification_type == 'mail') ? new MailNotification() : NULL;

        if ($error) {
            $this->notification->setError();
            $this->notification->setAddress($_ENV["ADMIN_MAIL"], $_ENV["ADMIN_NAME"]);
            $this->notification->addAttachment(Log::getFileName());
        }
    }

    function send()
    {
        $this->notification->send();
    }
}
