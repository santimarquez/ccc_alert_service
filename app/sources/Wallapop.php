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
        foreach ($array_response["search_objects"] as $key =>$ad) {
            $ads_array[$key] = new Advertisement();
            $ads_array[$key]->source_id = $this->source_id;
            $ads_array[$key]->reference = $ad["content"]["id"];
            $ads_array[$key]->price = $ad["content"]["price"];
            $ads_array[$key]->url = 'https://es.wallapop.com/item/' . $ad["content"]["web_slug"];
            $ads_array[$key]->pic_url = $ad["content"]["images"][0]["original"];

            $ads_array[$key]->kms = (isset($ad["content"]["km"])) ?  $ad["content"]["km"] : NULL;
            $ads_array[$key]->gear = ($ad["content"]["gearbox"] == "manual") ? "M" : "A";
            $ads_array[$key]->power = (isset($ad["content"]["horsepower"])) ? $ad["content"]["horsepower"] : NULL;
            $ads_array[$key]->year = $ad["content"]["year"];
        }

        return $ads_array;
    }
}
