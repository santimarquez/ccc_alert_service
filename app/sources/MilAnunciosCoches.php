<?php

Class MilAnunciosCoches
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
        
        if(count($exploded_stream) !== 2)
        {
            Log::add("The stream doesn't contain the expected data.");
            return false;
        }
        
        $exploded_stream = explode('");</script><script>window.__INITIAL_CONTEXT_VALUE__ ', $exploded_stream[1]);
              
        if(count($exploded_stream) !== 2)
        {
            Log::add("The stream doesn't contain the expected data.");
            return false;
        }

        $array_response = json_decode(stripslashes($exploded_stream[0]), true);
        
        //Create the response (array with Advertisement objects)
        $ads_array = array();
        $add_counter = 0;
        foreach($array_response["adListPagination"]["adList"]["ads"] as $ad)
        {
            $add_counter++;
            $ads_array[$add_counter] = new Advertisement();
            $ads_array[$add_counter]->source_id = $this->source_id;
            $ads_array[$add_counter]->reference = $ad["id"];
            $ads_array[$add_counter]->price = str_replace('.', '', $ad["price"]["value"]);
            $ads_array[$add_counter]->url = $ad["url"];
            if(isset($ad["images"][0]["src"]))
            {
                $ads_array[$add_counter]->pic_url = $ad["images"][0]["src"];
            }

            $ads_array[$add_counter]->phone_number = $ad["firstPhoneNumber"];
            
            foreach($ad["tags"] as $tag)
            {
                if($tag["type"] === "kms")
                {
                    $ads_array[$add_counter]->kms = str_replace(".", "", str_replace(" kms", "", $tag["text"]));
                }
                if($tag["type"] === "cambio")
                {
                    if($tag["text"] === "Manual")
                    {
                        $ads_array[$add_counter]->gear = "M";
                    }else{
                        $ads_array[$add_counter]->gear = "A";
                    }
                }
                if($tag["type"] === "CV")
                {
                    $ads_array[$add_counter]->power = str_replace(" CV", "", $tag["text"]);
                }
                if($tag["type"] === "au00F1o")
                {
                    $ads_array[$add_counter]->year = $tag["text"];
                }
            }
        }

        return $ads_array;
    }
}