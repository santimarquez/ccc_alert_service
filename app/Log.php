<?php
Class Log
{
    private $log_path = 'log/';
    public $file_pointer;
    
    function __construct()
    {
        $this->file_pointer = self::openFile();
    }

    /**
     * Open the file. It will place the pointer in the last position of 
     * the file in order to add more lines to it.
     *
     * @return string 
     */
    private function openFile()
    {
        $file_name = self::getFileName();
        $file_content = '';

        if(file_exists($file_name))
        {
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
        return $this->log_path . $current_date . '_errors.log';
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

        $line_to_add = date("Y-m-d H:i:s") . " - " . $string . "\r\n";
        if(fwrite($logger->file_pointer, $line_to_add))
        {
            return true;
        }else
        {
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
        if(fclose($this->file_pointer))
        {
            return true;
        }else
        {
            echo "Error saving the log file\r\n";
            return false;
        }
    }
}