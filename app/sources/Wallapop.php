<?php

Class Wallapop
{
    private $source_id = 2;

    /**
     * Parse the html response from the source
     * extracting the different ads captured.
     * It will return an array of objects type Advertisement.
     *
     * @param string $stream
     * @return array
     */
    public function specificParse($stream)
    {
        $array_response = json_decode($stream, true);

        //Create the response (array with Advertisement objects)
        $ads_array = array();
        $add_counter = 0;
        foreach ($array_response["search_objects"] as $ad) {
            $add_counter++;
            $ads_array[$add_counter] = new Advertisement();
            $ads_array[$add_counter]->source_id = $this->source_id;
            $ads_array[$add_counter]->reference = $ad["content"]["id"];
            $ads_array[$add_counter]->price = $ad["content"]["price"];
            $ads_array[$add_counter]->url = 'https://es.wallapop.com/item/' . $ad["content"]["web_slug"];
            $ads_array[$add_counter]->pic_url = $ad["content"]["images"][0]["original"];

            $ads_array[$add_counter]->phone_number = NULL;
            $ads_array[$add_counter]->kms = (isset($ad["content"]["km"])) ? $ad["content"]["km"] : NULL;
            ($ad["content"]["gearbox"] == "manual") ? $ads_array[$add_counter]->gear = "M" : $ads_array[$add_counter]->gear = "A";
            (isset($ad["content"]["horsepower"])) ? $ads_array[$add_counter]->power = $ad["content"]["horsepower"] : $ads_array[$add_counter]->power = NULL;
            $ads_array[$add_counter]->year = $ad["content"]["year"];
        }

        return $ads_array;
    }
}
