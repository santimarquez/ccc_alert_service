<?php

class Cochesnet
{
    private $source_id = 3;

    /**
     * Parse the html response from the source
     * extracting the different ads captured.
     * It will return a array of objects type Advertisement.
     *
     * @param string $stream
     * @return array
     */
    public function specificParse($stream)
    {
        //Get the JSON string and decode
        $exploded_stream = explode('window.__INITIAL_PROPS__ = JSON.parse("', $stream);
        if (count($exploded_stream) !== 2) {
            Log::add("The stream doesn't contain the expected data. source_id: " . $this->source_id);
            return false;
        }
        $exploded_stream = explode('");</script><script>window.__INITIAL_CONTEXT_VALUE__', $exploded_stream[1]);
        if (count($exploded_stream) !== 2) {
            Log::add("The stream doesn't contain the expected data. source_id: " . $this->source_id);
            return false;
        }

        $array_response = json_decode(stripslashes($exploded_stream[0]), true);

        //Create the response (array with Advertisement objects)
        $ads_array = array();
        foreach ($array_response["initialResults"]["items"] as $key => $ad) {
            $ads_array[$key] = new Advertisement();
            $ads_array[$key]->source_id = $this->source_id;
            $ads_array[$key]->reference = $ad["id"];
            $ads_array[$key]->title = $ad["title"];
            $ads_array[$key]->price = $ad["price"];
            $ads_array[$key]->url = $ad["url"];

            $ads_array[$key]->pic_url = (isset($ad["img"])) ? $ad["img"] : null;
            $ads_array[$key]->phone_number = (isset($ad["phone"])) ? $ad["phone"] : null;
            $ads_array[$key]->kms = (isset($ad["km"])) ? $ad["km"] : null;;
            $ads_array[$key]->gear =(strpos($ad["title"], "AT")) ?  "A" :  "M";
            if (strpos($ad["title"], "CV ")) {
                $title_exploded = explode("CV ", $ad["title"]);
                $title_exploded = explode(" ", $title_exploded[0]);
                $ads_array[$key]->power = $title_exploded[count($title_exploded) - 1];
            }
            $ads_array[$key]->year = $ad["year"];
        }

        return $ads_array;
    }
}
