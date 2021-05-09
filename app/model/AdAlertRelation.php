<?php

class AdAlertRelation
{
    public $alert_id;
    public $advertisement_id;

    /**
     * Returns an array with the list of
     * ads related to an specific alert.
     * It will use the ad id as 
     *
     * @param int $alert_id
     * @return array
     */
    static public function getAssocAds($alert_id)
    {
        $array = array();
        $result = Database::db('app_db', 'SELECT advertisement_id
                                          FROM dyn_db.ad_alert_relation
                                          WHERE alert_id = ' . $alert_id);
        if ($result) {
            while ($row = $result->fetch_object()) {
                if($row->advertisement_id != "")
                {
                    $array[$row->advertisement_id] = 1;
                }
            }
            return $array;
        }


        return false;
    }
    static public function countAssocAds($alert_id)
    {
        $result = Database::db('app_db', 'SELECT advertisement_id
                                          FROM dyn_db.ad_alert_relation
                                          WHERE alert_id = ' . $alert_id);

        return $result->num_rows;
    }

    /**
     * Will detect if the ad needs to be
     * updated or inserted and will
     * proceed correspondingly.
     *
     * @return mixed
     */
    public function save()
    {
        if (isset($this->id)) {
            return $this->update();
        }

        return $this->insert();
    }

    /**
     * Inserts a new relation to the database.
     * It returns the response from the Database class:
     * True if there was no error. False if there was an error.
     * 
     * @return boolean
     */
    private function insert()
    {
        $insert_field_str = '(';
        $insert_value_str = '(';

        foreach ($this as $property => $value) {
            $insert_field_str .= $property . ',';
            $insert_value_str .= '"' . $value . '",';
        }

        $insert_field_str = rtrim($insert_field_str, ',') . ')';
        $insert_value_str = rtrim($insert_value_str, ',') . ')';

        return Database::db('dyn_db', 'INSERT 
                                       INTO dyn_db.ad_alert_relation ' . $insert_field_str .
                                        ' VALUES ' . $insert_value_str, true);
    }

    /**
     * Update an already created relation.
     * This method will be executed if the object
     * has the propery "id" already set.
     *
     * @return boolean
     */
    private function update()
    {
        //TODO
        return false;
    }
}
