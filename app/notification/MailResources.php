<?php

class MailResources
{

    public $toAddress = array();
    public $containerable = array();
    public $picture = array();

    public function verify()
    {
        foreach ($this as $property_name => $content) {
            if (!$this->$property_name($content)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Function that will validate that
     * the entered resource is valid for the address field
     * Must be a instance of an user o a array of instances.
     * 
     * @param array $content
     * @return boolean
     */
    private function toAddress($content)
    {
        if (is_array($content)) {
            foreach ($content as $datum) {
                if (!($datum instanceof User)) {
                    Log::add("The toAddress field on resources is not valid.");
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Function that will validate that
     * the entered resource is valid for conteinarable field
     * Must be an object or an array of objects.
     *
     * @param array $content
     * @return boolean
     */
    private function containerable($content)
    {
        if (is_object($content)) {
            return true;
        }

        if (is_array($content)) {
            foreach ($content as $datum) {
                if (!is_object($datum)) {
                    Log::add("The containerable field on resources is not valid.");
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Function that will validate that the entered resource is valid
     * for the picture field.
     * Must be empty, an string or an array of strings.
     *
     * @param void|string|array $content
     * @return boolean
     */
    private function picture($content)
    {
        if (empty($content) || $content == '') {
            return true;
        }

        if (is_string($content)) {
            return true;
        }

        if (is_array($content)) {
            foreach ($content as $datum) {
                if (!is_string($datum)) {
                    Log::add("The picture field on resources is not valid.");
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Set the recipent for the email notification
     *
     * @param object|array $user
     */
    public function setAddress($user)
    {
        $this->toAddress[] = $user;
    }

    /**
     * Adds to an array all the objects that will
     * be included in containers in the email.
     * For example: each advertisement
     *
     * @param object $containerable
     * @return void
     */
    public function setContainer($containerable)
    {
        $this->containerable[] = $containerable;
    }

    /**
     * Adds to an array all the objects that will
     * be included in containers in the email.
     * For example: each advertisement
     *
     * @param string $picture
     */
    public function setPicture($picture)
    {
        $current_idx = count($this->picture) - 1;
        $picture_info = pathinfo($picture);
        $file_name =  basename($picture, '.' . $picture_info['extension']);
        $this->picture[$current_idx]['url'] = $picture;
        $this->picture[$current_idx]['cid'] = $file_name;
    }
}
