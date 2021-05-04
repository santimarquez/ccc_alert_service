<?php

class Notification
{
    public $notification;
    private $notification_type;
    public $resources;
    private $error_flag = false;

    /**
     * Create the notification.
     * Default: mail
     *
     * @param string $notification_type
     * @param array $resources
     */
    function __construct($notification_type = 'mail', $resources = NULL)
    {
        $this->notification = ($notification_type == 'mail') ? new MailNotification() : NULL;
        $this->resources = $resources;
    }

    /**
     * Send the notification (independently of the type)
     *
     * @return boolean
     */
    function send()
    {
        if (!$this->error_flag) {
            $this->notification->send();
            return true;
        }

        return false;
    }

    /**
     * Set the notification type.
     * It will define the view and backed used, which will prepare
     * all the necessary information for sending the email.
     * 
     * It will need 
     *
     * @param string $notification_name
     * @return boolean
     */
    function setType($notification_name)
    {
        try {
            $this->notification_type = $notification_name;
            $this->notification->importBackend($notification_name, $this->resources);
            $this->notification->loadView($notification_name, $this->resources);

            if (empty($this->resources) && $this->notification->resources_required) {
                Log::add('The notification type required resources, but it was empty.');
                $this->error_flag = true;
                return false;
            }
        } catch (Exception $e) {
            Log::add('Error setting the notification type. The notification won\'t be sent.');
            $this->error_flag = true;
            return false;
        }

        return true;
    }
}
