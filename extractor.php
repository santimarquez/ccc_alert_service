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
include 'autoload.php';

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
if (!$alert_list) {
    $critical_error = true;
    goto log_critical;
}

if ($alert_list->num_rows ==  0) {
    Log::add("There are no enabled alerts.");
} else {
    Log::add($alert_list->num_rows . " alerts are being managed.");
}

//Log alerts data
$logger->nr_alerts = $alert_list->num_rows;

while ($alert = $alert_list->fetch_object()) {

    //Setup the necessary information for the alert:
    $ads_to_alert = array();


    Log::add("***  Managing alert $alert->id  ***");

    /**
     * Get the already related ads list
     */
    $alert_related_ads = AdAlertRelation::getAssocAds($alert->id);
    $alert_num_related_ads = count($alert_related_ads);

    /**
     * Get the list of sources
     */
    $source_list = Source::getSources();
    while ($source = $source_list->fetch_object()) {
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
        if ($alert_url->url == $source->url . '?') {
            goto end;
        }

        Log::add('Getting data from: ' . $alert_url->url);

        $html = new Html();
        $html->retrieve($alert_url->url);

        if (!$html->exist) {
            $critical_error = true;
            goto log_critical;
        }

        $ads = $html->parse($source->id);

        /**
         * Analyze the parsed ads and insert or
         * update if any change has been detected
         * or there's a new ad found.
         * 
         * It will analyze the vehicle color if necessary.
         * 
         */
        foreach ($ads as $ad) {
            $logger->ads_found++;
            if ($found_ad = Advertisement::findUnique($ad->source_id . $ad->reference)) {
                $ad->id = $found_ad->id;

                //Add the ad to the alert's related ads:
                if (!isset($alert_related_ads[$ad->id])) {
                    $alert_add_relation = new AdAlertRelation();
                    $alert_add_relation->alert_id = $alert->id;
                    $alert_add_relation->advertisement_id = $ad->id;
                    if ($alert_add_relation->save() && $alert_num_related_ads > 0) {
                        $ads_to_alert[] = $ad;
                    }
                }

                if ($ad != $found_ad) {
                    if ($ad->save()) {
                        $logger->ads_updated++;
                    } else {
                        $logger->errors_found++;
                    }
                } else {
                    $logger->ads_ignored++;
                }
            } else {

                //Extract the color:
                if ($_ENV['EXTRACT_COLOR'] == 'true') {
                    if ($ad->pic_url !== NULL) {
                        $picture = new ImageParser();
                        $picture->load($ad->pic_url);
                        $ad->color = $picture->parse();
                    }
                }

                if ($ad->id = $ad->save()) {
                    $logger->ads_inserted++;

                    //Insert the alert-ad relation:
                    $alert_add_relation = new AdAlertRelation();
                    $alert_add_relation->alert_id = $alert->id;
                    $alert_add_relation->advertisement_id = $ad->id;
                    if ($alert_add_relation->save() && $alert_num_related_ads > 0) {
                        $ads_to_alert[] = $ad;
                    }
                } else {
                    $logger->errors_found++;
                }
            }
        }
    }

    /**
     * 
     * Parse the $ads_to_alert
     * to remove those that doesn't fit on
     * the requirements
     * 
     */

    $alert_info = Alert::findExtended($alert->id);
    foreach ($ads_to_alert as $key => $ad) {
        if ($ad->price > $alert_info->trigger_price) {
            unset($ads_to_alert[$key]);
        }
    }

    /**
     * 
     * If there are ads that meet the alert criteria,
     * prepare the necessary resources and send email.
     * 
     */

    if (!empty($ads_to_alert)) {
        Log::add(" Alerts found, launch the process to send notification...");
        try {
            //Prepare the resources to be sent:
            $resources = new MailResources();
            $resources->setAddress(User::find($alert->user_id));
            foreach ($ads_to_alert as $ad) {
                $resources->setContainer($ad);
            }
            $resources->verify();

            //Send email:
            $notification = new Notification('mail', $resources);
            $notification->setType('newAdvertisement');
            if ($notification->send()) {
                Log::add("Notification sent to the user: at least one ad meets the requirements");
            }
        } catch (Exception $e) {
            Log::add("An alert should have been sent, but an error was found.");
            $critical_error = true;
            goto log_critical;
        }
    }
}

/**
 * Section to log any critical error found
 */
log_critical:
if ($critical_error) {
    $logger->critical_error = true;
    Log::add("ERROR: THE SCRIPT DIDN'T FINISH DUE TO A CRITICAL ERROR.");
}
goto end;

end:
/**
 * Log the service execution end and finish the script execution
 */
$logger->end_timestamp = date('Y-m-d H:i:s');
$logger->recordDB();
Log::add("-------------------------- END OF THE PROCESS --------------------------\n\n");

if ($critical_error) {
    $notification = new Notification('mail');
    $notification->setType('error');
    $notification->send();
}
