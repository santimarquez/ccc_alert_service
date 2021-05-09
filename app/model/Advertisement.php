<?php

class Advertisement
{
    public $id;
    public $source_id;
    public $reference;
    public $price;
    public $url;
    public $pic_url;
    public $color;
    public $phone_number;
    public $kms;
    public $gear;
    public $power;
    public $year;

    /**
     * Find an specific advertisement based on the id
     * and returns a new instance with it's properties.
     *
     * @param int $id
     * @return object|boolean
     */
    static function find($id)
    {
        $advertisement = new Advertisement();
        $result = Database::db('dyn_db', 'SELECT *
                                          FROM dyn_db.advertisement
                                          WHERE id = ' . $id);
        $row = $result->fetch_object();
        if (mysqli_num_rows($result) === 1) {
            foreach ($advertisement as $property => $value) {
                $advertisement->$property = $row->$property;
            }
            return $advertisement;
        }

        return false;
    }

    /**
     * Find an specific advertisement based on the unique ad code (uac)
     * and returns a new instance with it's properties.
     *
     * @param int $uac_id
     * @return object|boolean
     */
    static function findUnique($uac_id)
    {
        $advertisement = new Advertisement();
        $result = Database::db('dyn_db', 'SELECT *
                                          FROM dyn_db.advertisement
                                          WHERE uac = "' . $uac_id . '"');

        $row = $result->fetch_object();
        if (mysqli_num_rows($result) === 1) {
            foreach ($advertisement as $property => $value) {
                $advertisement->$property = $row->$property;
            }
            return $advertisement;
        }

        return false;
    }


    /**
     * Returns true or false depending if the ad
     * has been found in the database based on id
     *
     * @param int $id
     * @return boolean
     */
    static function exists($id)
    {
        $result = Database::db('dyn_db', 'SELECT id
                                          FROM dyn_db.advertisement
                                          WHERE id = ' . $id);
        if (mysqli_num_rows($result) === 1) {
            return true;
        }

        return false;
    }

    /**
     * Returns true or false depending if the ad
     * has been found in the database based on uac
     *
     * @param int $uac_id
     * @return boolean
     */
    static function existsUnique($uac_id)
    {
        $result = Database::db('dyn_db', 'SELECT id
                                          FROM dyn_db.advertisement
                                          WHERE uac = "' . $uac_id . '"');
        if (mysqli_num_rows($result) === 1) {
            return true;
        }

        return false;
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
     * Inserts a new advertisement to the database.
     * It returns the response from the Database class:
     * True if there was no error. False if there was an error.
     * 
     * @return boolean
     */
    private function insert()
    {
        $error = true;
        $insert_field_str = '(';
        $insert_value_str = '(';

        foreach ($this as $property => $value) {
            if ($value !== NULL) {
                $error = false;
                $insert_field_str .= $property . ',';
                $insert_value_str .= '"' . $value . '",';
            }
        }

        $insert_field_str = rtrim($insert_field_str, ',') . ')';
        $insert_value_str = rtrim($insert_value_str, ',') . ')';

        if ($error) {
            Log::add("Error inserting advertisement. Reg completely NULL.");
            return false;
        }

        return Database::db('dyn_db', 'INSERT IGNORE 
                                       INTO dyn_db.advertisement ' . $insert_field_str .
            ' VALUES ' . $insert_value_str, true);
    }

    /**
     * Update an already created advertisement.
     * This method will be executed if the object
     * has the propery "id" already set.
     *
     * @return boolean
     */
    private function update()
    {
        try {
            $set_str = '';
            foreach ($this as $property => $value) {
                if ($property === "id") continue;
                if ($value === NULL || $value == "") {
                    $set_str .= $property . ' = NULL,';
                } else {
                    $set_str .= $property . ' = "' . $value . '",';
                }
            }

            $set_str = rtrim($set_str, ',');

            return Database::db('dyn_db', 'UPDATE dyn_db.advertisement SET ' . $set_str .
                ' WHERE id = ' . $this->id);
        } catch (Exception $e) {
            return false;
        }
    }
}
