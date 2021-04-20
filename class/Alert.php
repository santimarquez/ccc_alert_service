<?php

Class Alert
{
    /*
     * Returns an object with the list of alerts that must be executed 
     */
    static function getAlertList()
    {
        //Connecting the DB
        $mysqli = new mysqli("localhost", "root", "root", "dyn_db");
    
        if ($mysqli->connect_errno) {
            echo "Error connecting to MysQLL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        }
    
        //Get the list of active alerts
        $alerts_query =
        '   SELECT a.id, u.email
            FROM alert a, user u
            WHERE a.user_id = u.id
            AND a.enabled = 1'
        ;
    
        //Manage query errors...
        if (!$results = $mysqli->query($alerts_query)) {    
            // De nuevo, no hacer esto en un sitio público, aunque nosotros mostraremos
            // cómo obtener información del error
            echo "Error: The query failed due to: \n";
            echo "Query: \n " . $alerts_query . "\n";
            echo "Errno: " . $mysqli->errno . "\n";
            echo "Error: " . $mysqli->error . "\n";
            exit;
        }
    
        return $results;
    }
}

?>