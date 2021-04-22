<?php

Class Alert
{
    private $logger;

    /**
     * Get the list of alerts.
     * Returns an array wil the alert id and the email of the 
     * user that created the alert.
     *
     * @return array
     */
    static function getAlertList()
    {
        $result = Database::db('dyn_db', 'SELECT a.id, u.email 
                                            FROM alert a, user u 
                                            WHERE a.user_id = u.id 
                                            AND a.enabled = 1');
        return $result;
    }

    /**
     * Get one specific alert based on the alert id.
     *
     * @param [int] $id
     * @return object
     */
    static function find($id)
    {
        $result = Database::db('dyn_db', 'SELECT a.id, u.email 
                                            FROM alert a, user u 
                                            WHERE a.user_id = u.id 
                                            AND a.id = ' . $id);
        return $result;
    }
}

?>