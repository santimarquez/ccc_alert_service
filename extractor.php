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
    $class_root_path = 'app\\';
    if(is_file($class_root_path . $class_name . '.php'))
    {
        include $class_root_path . $class_name . '.php';
    }else{
        foreach(glob($class_root_path . '*', GLOB_ONLYDIR) as $dir)
        {
            if(is_file($dir . '\\' . $class_name . '.php'))
            {
                include $dir . '\\' . $class_name . '.php';
            }
        }
    }
});

/**
 * Load the environment variables
 */
(new DotEnv(__DIR__ . '/.env'))->load();

/**
 * Create control variables
 */

 $logger = new Log();
 $critical_error = false;

/**
 * Log the service execution kick off.
 */
Log::add("----------------------- STARTING THE PROCESS EXECUTION -----------------------");

$alert_list = Alert::getAlertList();

//Error getting the alert list
if(!$alert_list)
{
    $critical_error = true;
    goto log_critical;
}


if ($alert_list->num_rows ==  0) 
{
    Log::add("There are no enabled alerts.");
}

//Log alerts data
$logger->nr_alerts = $alert_list->num_rows;
Log::add($alert_list->num_rows . " alerts are being managed.");

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

        if(!$html->exist)
        {
            $critical_error = true;
            goto log_critical;
        }

        $ads = $html->parse($source->id);
        
        /**
         * Analize the parsed ads and insert or
         * update if any change has been detected
         * or it's a new ad found.
         * 
         * It will analyze the vehicle color if necessary.
         * 
         */
        foreach($ads as $ad)
        {
            $logger->ads_found++;
            if($found_ad = Advertisement::findUnique($ad->source_id.$ad->reference))
            {
                $ad->id = $found_ad->id;
                if($ad != $found_ad)
                {
                    if($ad->save())
                    {
                        $logger->ads_updated++;
                    }else{
                        $logger->errors_found++;
                    }
                }else{
                    $logger->ads_ignored++;
                }
            }
            else{
                
                //Extract the color:
                $picture = new ImageParser();
                $picture->load($ad->pic_url);
                $ad->color = $picture->parse();

                if($ad->save())
                {
                    $logger->ads_inserted++;
                }else{
                    $logger->errors_found++;
                }
            }
        }
    }
}

/**
 * Section to log any critical error found
 */
log_critical:
if($critical_error)
{
    $logger->critical_error = true;
    Log::add("ERROR: THE SCRIPT DIDN'T FINISH DUE TO A CRITICAL ERROR.");
}
goto end;

/**
 * Finish the script execution
 */
end:
/**
 * Log the service execution end.
 */
$logger->end_timestamp = date('Y-m-d H:i:s');
$logger->recordDB();
Log::add("-------------------------- END OF THE PROCESS --------------------------\n\n");