<?php
/*
@author Santi Márquez
@date 2021-04-21
@email santymaro@gmail.com

This file will contains the script in charge of retrieving the data from
the sources created in the database.

It will be doing the extractions based on the alerts created.

*/

/**
 * Firstly, add the classes autoload
 */
spl_autoload_register(function ($class_name) {
    include 'class/' . $class_name . '.php';
});

/**
 * Load the environment variables
 */
(new DotEnv(__DIR__ . '/.env'))->load();

/**
 * Create control variables
 */

 $critical_error = false;

/**
 * Log the service execution kick off.
 */
$logger = new Log();
$logger->add("----------------------- STARTING THE PROCESS EXECUTION -----------------------");
$logger->save();

$alert_list = Alert::getAlertList();

if(!$alert_list)
{
    $critical_error = true;
    goto log_critical;
}

if ($alert_list->num_rows ==  0) {
    $logger = new Log();
    $logger->add("There are no enabled alerts.");
    $logger->save();
}

$logger = new Log();
$logger->add($alert_list->num_rows . " alerts are being managed.");
$logger->save();

while($alert = $alert_list->fetch_object()) {
    /**
     * Get the list of sources
     */
    $source_list = Source::getSources();
    while($source = $source_list->fetch_object()) {
        /**
         * Prepare the destination URL
         * and retrieve the HTML content
         */
        $alert_url = new Url($source->url);
        $alert_url->append(AlertFilter::getFilters($alert->id, $source->id));

        /**
         * Retrieve HTML content if the system
         * found available get variables
         * for the filters in the selected source
         */
        if($alert_url->url == $source->url . '?')
        {
            goto end;
        }

        $html = new Html();
        $html->retrieve($alert_url->url);

        echo $html->stream;
        /**
         * If no html has been retrieve, throw 
         * a critical error and stop the execution
         */
        if(!$html->exist)
        {
            $critical_error = true;
            goto log_critical;
        }
    }
}

/**
 * Section to log any critical error found
 */
log_critical:
if($critical_error)
{
    $logger = new Log();
    $logger->add("ERROR: THE SCRIPT DIDN'T FINISH DUE TO A CRITICAL ERROR.");
    $logger->save();
}
goto end;

/**
 * Finish the script execution
 */
end:
/**
 * Log the service execution end.
 */
$logger = new Log();
$logger->add("-------------------------- END OF THE PROCESS --------------------------\n\n");
$logger->save();