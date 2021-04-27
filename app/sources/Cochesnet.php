<?php

Class Cochesnet
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
        if(count($exploded_stream) !== 2)
        {
            Log::add("The stream doesn't contain the expected data. source_id: " . $this->source_id);
            return false;
        }
        $exploded_stream = explode('");</script><script>window.__INITIAL_CONTEXT_VALUE__', $exploded_stream[1]);
        if(count($exploded_stream) !== 2)
        {
            Log::add("The stream doesn't contain the expected data. source_id: " . $this->source_id);
            return false;
        }
        
        $array_response = json_decode(stripslashes($exploded_stream[0]), true);

        //Create the response (array with Advertisement objects)
        $ads_array = array();
        $add_counter = 0;
        foreach($array_response["initialResults"]["items"] as $ad)
        {
            $add_counter++;
            $ads_array[$add_counter] = new Advertisement();
            $ads_array[$add_counter]->source_id = $this->source_id;
            $ads_array[$add_counter]->reference = $ad["id"];
            $ads_array[$add_counter]->price = $ad["price"];
            $ads_array[$add_counter]->url = $ad["url"];
            $ads_array[$add_counter]->pic_url = $ad["img"];
            
            $ads_array[$add_counter]->phone_number = $ad["phone"];
            $ads_array[$add_counter]->kms = $ad["km"];
            (strpos($ad["title"],"AT")) ? $ads_array[$add_counter]->gear = "A" : $ads_array[$add_counter]->gear = "M";
            if(strpos($ad["title"],"CV "))
            {
                $title_exploded = explode("CV ", $ad["title"]);
                $title_exploded = explode(" ", $title_exploded[0]);
                $ads_array[$add_counter]->power = $title_exploded[count($title_exploded) - 1];
            }
            $ads_array[$add_counter]->year = $ad["year"];
        }

        return $ads_array;
    }
}