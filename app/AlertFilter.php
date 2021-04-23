<?php

Class AlertFilter
{
    /**
     * Get the list of alerts available for the selected
     * alert.
     * 
     * @param [int] $alert_id
     * @param [int] $source_id
     * @return object
     */
    static public function getFilters($alert_id, $source_id)
    {
        $result = Database::db('dyn_db', 'SELECT * 
                                          FROM alert_filter_list 
                                          WHERE alert_id = ' . $alert_id . '
                                          AND source_id = ' . $source_id);
        return $result;
    }
}