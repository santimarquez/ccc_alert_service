<?php

class Database
{
    private function connect()
    {
        //Connecting the DB
        $mysqli = new mysqli($_ENV['DYN_DB_HOST'], $_ENV['DYN_DB_USER'], $_ENV['DYN_DB_PASSWORD'], $_ENV['DYN_DB_NAME']);

        if ($mysqli->connect_errno) {
            echo "Error connecting to MysQLL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
    }
}