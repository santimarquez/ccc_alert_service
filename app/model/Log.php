<?php
class Log
{
    private $file_pointer;

    public $nr_alerts;
    public $ads_found;
    public $ads_inserted;
    public $ads_updated;
    public $ads_ignored;
    public $errors_found;
    public $critical_error;
    public $start_timestamp;
    public $end_timestamp;

    function __construct()
    {
        $this->nr_alerts = 0;
        $this->ads_found = 0;
        $this->ads_inserted = 0;
        $this->ads_updated = 0;
        $this->ads_ignored = 0;
        $this->errors_found = 0;
        $this->critical_error = false;
        $this->start_timestamp = date('Y-m-d H:i:s');
    }

    /**
     * Open the file. It will place the pointer in the last position of 
     * the file in order to add more lines to it.
     *
     * @return string 
     */
    private function openFile()
    {
        $file_name = $this->getFileName();
        $file_content = '';

        if (file_exists($file_name)) {
            $file_content = file_get_contents($file_name);
        }

        $file_pointer = fopen($file_name, 'w');
        fwrite($file_pointer, $file_content);

        return $file_pointer;
    }

    /**
     * Prepare the file name including today's date
     *
     * @return string
     */
    private function getFileName()
    {
        $current_date = date("Ymd");
        return $_ENV['LOG_PATH'] . $current_date . '_errors.log';
    }

    /**
     * Add time print and the string received as parameter
     * to the file
     *
     * @param [string] $string
     * @return bolean
     */
    static public function add($string)
    {
        $logger = new Log();

        $logger->file_pointer = $logger->openFile();

        $line_to_add = date("Y-m-d H:i:s") . " - " . $string . "\r\n";
        if (fwrite($logger->file_pointer, $line_to_add)) {
            return true;
        } else {
            echo "Error adding record to the log file\r\n";
            return false;
        }

        $logger->save();
    }

    /**
     * Closes the file
     *
     * @return boolean
     */
    public function save()
    {
        if (fclose($this->file_pointer)) {
            return true;
        } else {
            echo "Error saving the log file\r\n";
            return false;
        }
    }

    /**
     * Save the data recorded during
     * the script execution in the 
     * app_db.log table.
     *
     * @return boolean
     */
    public function recordDB()
    {
        $insert_field_str = '(';
        $insert_value_str = '(';

        foreach ($this as $property => $value) {
            if ($property == 'file_pointer') {
                continue;
            }
            if ($value !== NULL) {
                $insert_field_str .= $property . ',';
                $insert_value_str .= '"' . $value . '",';
            }
        }

        $insert_field_str = rtrim($insert_field_str, ',') . ')';
        $insert_value_str = rtrim($insert_value_str, ',') . ')';


        return Database::db('dyn_db', 'INSERT IGNORE 
                                       INTO app_db.log ' . $insert_field_str .
            ' VALUES ' . $insert_value_str);
    }
}
