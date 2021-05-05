<?php

class MilAnunciosCoches
{
    private $source_id = 1;

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
            Log::add("The stream doesn't contain the expected data.");
            return false;
        }

        $exploded_stream = explode('");</script><script>window.__INITIAL_CONTEXT_VALUE__ ', $exploded_stream[1]);

        if (count($exploded_stream) !== 2) {
            Log::add("The stream doesn't contain the expected data.");
            return false;
        }

        $array_response = json_decode(stripslashes($exploded_stream[0]), true);

        //Create the response (array with Advertisement objects)
        $ads_array = array();
        foreach ($array_response["adListPagination"]["adList"]["ads"] as $key => $ad) {
            $ads_array[$key] = new Advertisement();
            $ads_array[$key]->source_id = $this->source_id;
            $ads_array[$key]->reference = $ad["id"];
            $ads_array[$key]->title = $ad["title"];
            $ads_array[$key]->price = str_replace('.', '', $ad["price"]["value"]);
            $ads_array[$key]->url = $ad["url"];

            $ads_array[$key]->pic_url = (isset($ad["images"][0]["src"])) ? $ad["images"][0]["src"] : NULL;
            $ads_array[$key]->phone_number = (isset($ad["firstPhoneNumber"])) ? $ad["firstPhoneNumber"] : NULL;
            foreach ($ad["tags"] as $tag) {
                if ($tag["type"] === "kms") {
                    $ads_array[$key]->kms = str_replace(".", "", str_replace(" kms", "", $tag["text"]));
                }
                if ($tag["type"] === "cambio") {
                    if ($tag["text"] === "Manual") {
                        $ads_array[$key]->gear = "M";
                    } else {
                        $ads_array[$key]->gear = "A";
                    }
                }
                if ($tag["type"] === "CV") {
                    $ads_array[$key]->power = str_replace(" CV", "", $tag["text"]);
                }
                if ($tag["type"] === "au00F1o") {
                    $ads_array[$key]->year = $tag["text"];
                }
            }
        }

        return $ads_array;
    }
}
