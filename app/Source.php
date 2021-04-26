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
        $result = Database::db('app_db', 'SELECT * FROM source WHERE enabled = 1');
        return $result;
    }

    /**
     * Get the short source's description from the
     * source table.
     *
     * @param [int] $source_id
     * @return string
     */
    static public function getShortDescription($source_id)
    {
        $result = Database::db('app_db', 'SELECT short_desc FROM source WHERE id = ' . $source_id);
        $data = $result->fetch_object();
        return $data->short_desc;
    }
}