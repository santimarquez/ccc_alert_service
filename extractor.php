<?php
spl_autoload_register(function ($class_name) {
    include 'class/' . $class_name . '.php';
});

/*

This file will contain the script in charge of retrieve the data from
the sources created in the database.

It will be doing the extractions based on the alerts created.

*/

$results = Alert::getAlertList();

while ($row = $results->fetch_assoc()) {
    echo " id = " . $row['id'] . " | user = " . $row['email'] . "\n";
}