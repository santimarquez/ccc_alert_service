<?php

Class Source
{
    /**
     * 
     * Get the list of sources.
     *
     * @return void
     */
    static public function getSources()
    {
        $result = Database::db('app_db', 'SELECT * FROM source');
        return $result;
    }
}