<?php

/**
 * 
 * Section to set the static data.
 * Set the variable $this->resources_required as true
 * in case that data as the toAddress will be received
 * dynamically.
 * 
 * Otherwise, it will be sent to the server admin.
 * 
 */
$this->resources_required = false;
$this->footer_layout = 'warning';
$this->header_layout = 'warning';

/**
 * 
 * Notification configurable variables
 * 
 */

//Mail subject
$this->mail->Subject = '[!IMPORTANT] Error detected on Alerts Service';

//Mail attachments
$this->addAttachment(Log::getFileName());

/**
 * 
 * Set all the information.
 * THIS SECTION DOESN'T NEED TO BE MODIFIED.
 * 
 */
if (empty($resources) && $this->resources_required) {
    return false;
}

/**
 * Set the Address to whom the email
 * will be sent
 */
if (empty($resources['toAddress'])) {
    $this->setAddress($_ENV['ADMIN_MAIL'], $_ENV['ADMIN_NAME']);
} else {
    foreach ($resources['toAddress'] as $data) {
        $this->setAddress($data['email'], $data['name']);
    }
}
