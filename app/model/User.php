<?php

class User
{
    public $id;
    public $email;

    static function find($id)
    {
        $instance = new self();
        $result = Database::db('dyn_db', 'SELECT *
                                          FROM dyn_db.user
                                          WHERE id = ' . $id);
        $row = $result->fetch_object();
        if (mysqli_num_rows($result) === 1) {
            foreach ($instance as $property => $value) {
                $instance->$property = $row->$property;
            }
            return $instance;
        }

        return false;
    }
}
