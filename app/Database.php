<?php

class Database
{

    /**
     * Do the connection with the MySQL database.
     * It will log any error ocurred.
     * It returns the connector.
     *
     * @param [string] $host
     * @param [string] $user
     * @param [string] $pass
     * @param [string] $database
     * @return object
     */
    static private function connect($host, $user, $pass, $database)
    {
        $mysqli = new mysqli($host, $user, $pass, $database);

        if ($mysqli->connect_errno) {
            Log::add("Error connecting to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
            return false;
        }

        return $mysqli;
    }

    /**
     * Launch the query against the given database.
     *
     * @param [object] $mysqli
     * @param [string] $query
     * @return object
     */
    static private function exec($mysqli, $query)
    {
        /**
         * Execute query and return results.
         */
        if (!$results = $mysqli->query($query)) {
            Log::add("Error: The query failed due to: \nQuery: \n " . $query . "\nErrno: " . $mysqli->errno . "\nError: " . $mysqli->error . "\n");
            return false;
        }
        return $results;
    }

    /**
     * Select the database and connect.
     * It will log an error if the selected database
     * doesn't exist
     *
     * @param string $database
     * @param string $query
     * @param boolean $insert_flag
     * @return mixed|object|int
     */
    static public function db($database, $query = NULL, $insert_flag = NULL)
    {
        /**
         * Prepare the credentials configured in the .env file
         * for the selected database.
         */
        $database = strtoupper($database);
        $host = $_ENV[$database . '_HOST'];
        $user = $_ENV[$database . '_USER'];
        $password = $_ENV[$database . '_PASSWORD'];

        /**
         * Return error if there are no available credentials
         */
        if (!$host || !$user || !$password) {
            Log::add("No credentials found for the selected database: $database.\nPossible issue with the .env file.");
            return false;
        }

        $mysqli = self::connect($host, $user, $password, $database);

        /**
         * If there is an input query return results,
         * otherwise return the database connector.
         */
        if ($query) {
            if ($insert_flag) {
                self::exec($mysqli, $query);
                $result = self::exec($mysqli, 'SELECT LAST_INSERT_ID()');
                $response = $result->fetch_row();
                return $response[0] ?? false;
            } else {
                return self::exec($mysqli, $query);
            }
        }

        return $mysqli;
    }
}
