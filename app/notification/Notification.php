<?php

class Notification
{
    public $notification;

    function __construct($notification_type = 'mail', $error = NULL)
    {
        $this->notification = ($notification_type == 'mail') ? new MailNotification() : NULL;
        
        if ($error) {
            $this->notification->setError();
        }
    }
}
