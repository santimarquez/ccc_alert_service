<?php

Class MilAnunciosCoches
{
    private $source_id = 1;

    public function specificParse($stream)
    {
        $exploded_stream = explode('window.__INITIAL_PROPS__ = JSON.parse("', $stream);
        $exploded_stream = explode('");</script><script>window.__INITIAL_CONTEXT_VALUE__ ', $exploded_stream[1]);
        $array_response = json_decode(stripslashes($exploded_stream[0]), true);
        
        $adds_array = array();
        foreach($array_response["adListPagination"]["adList"]["ads"] as $ad)
        {
            $advertisement = new Advertisement();

            $advertisement->source_id = $this->source_id;
            $advertisement->reference = $ad["id"];
            $advertisement->price = str_replace('.', '', $ad["price"]["value"]);
            $advertisement->url = $ad["url"];
            if(isset($ad["images"][0]["src"]))
            {
                $advertisement->pic_url = $ad["images"][0]["src"];
            }
            
            $advertisement->phone_number = $ad["firstPhoneNumber"];
            
            foreach($ad["tags"] as $tag)
            {
                if($tag["type"] === "kms")
                {
                    $advertisement->kms = str_replace(".", "", str_replace(" kms", "", $tag["text"]));
                }
                if($tag["type"] === "cambio")
                {
                    if($tag["text"] === "Manual")
                    {
                        $advertisement->gear = "M";
                    }else{
                        $advertisement->gear = "A";
                    }
                }
                if($tag["type"] === "CV")
                {
                    $advertisement->power = str_replace(" CV", "", $tag["text"]);
                }
                if($tag["type"] === "au00F1o")
                {
                    $advertisement->year = $tag["text"];
                }
            }

            $advertisement->save();
        }
    }
}