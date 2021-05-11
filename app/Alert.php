<?php

class Alert
{

    /**
     * Get the list of alerts.
     * Returns an array wil the alert id and the email of the 
     * user that created the alert.
     *
     * @return object
     */
    static public function getAlertList()
    {
        $result = Database::db('dyn_db', 'SELECT a.id, a.user_id, u.email 
                                            FROM alert a, user u 
                                            WHERE a.user_id = u.id 
                                            AND a.enabled = 1');
        return $result;
    }

    /**
     * Get one specific alert based on the alert id.
     *
     * @param int $id
     * @return object
     */
    static public function find($id)
    {
        $result = Database::db('dyn_db', 'SELECT a.id, u.email 
                                            FROM alert a, user u 
                                            WHERE a.user_id = u.id 
                                            AND a.id = ' . $id);
        return $result;
    }

    /**
     * Get the extended information of a specific alert,
     * which includes the average price, the trigger price...
     *
     * @param int $id
     * @return object
     */
    static public function findExtended($id)
    {
        $result = Database::db('dyn_db', 'SELECT *
                                          FROM dyn_db.active_alerts
                                          WHERE id = ' . $id);

        return $result->fetch_object();
    }
}
