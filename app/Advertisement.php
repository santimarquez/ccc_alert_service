<?php

Class Advertisement
{
    public $source_id;
    public $reference;
    public $price;
    public $url;
    public $pic_url;
    public $phone_number;
    public $kms;
    public $gear;
    public $power;
    public $year;

    public function save()
    {
        $error = true;
        $insert_field_str = '(';
        $insert_value_str = '(';

        foreach($this as $property => $value)
        {
            if($value !== NULL)
            {
                $error = false;
                $insert_field_str .= $property . ',';
                $insert_value_str .= '"' . $value . '",';
            }
            
        }
        
        $insert_field_str = rtrim($insert_field_str, ',') . ')';
        $insert_value_str = rtrim($insert_value_str, ',') . ')';

        if($error)
        {
            $logger = new Log();
            $logger->add("Error inserting advertisement. Reg completely NULL.");
            $logger->save();
            return false;
        }

        return Database::db('dyn_db', 'INSERT INTO advertisement ' . $insert_field_str . ' VALUES ' . $insert_value_str);
    }
}