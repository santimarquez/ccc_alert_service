<?

Class AlertFilter
{
    /**
     * Get the list of alerts available for the selected
     * alert.
     *
     * @param [int] $source_id
     * @return object
     */
    static public function getFilters($source_id)
    {
        $result = Database::db('dyn_db', 'SELECT * FROM alert_filter_list');
        return $result;
    }
}