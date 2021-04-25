<?php

Class Html
{
    public $exist;
    public $stream;
    public $structure;

    function __construct($url = NULL)
    {
        if($url === NULL)
        {
            $this->stream = '';
            $this->exist = false;
        }
    }

    /**
     * Generate and execute de cURL request
     * to get the HTML stream.
     * Store the content in the propierty stream
     * Fill the exist switch.
     *
     * @param [string] $url
     * @return void
     */
    public function retrieve($url)
    {
        $client = curl_init();

        curl_setopt($client, CURLOPT_URL, $url . '&orden=baratos');
        curl_setopt($client, CURLOPT_HEADER, 0);
        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

        if(!$html = curl_exec($client))
        {
            Log::add('Curl error: ' . curl_error($client));
            
            curl_close($client);
            
        }

        curl_close($client);
        
        if(!$html)
        {
            Log::add('Error getting the html from: ' . $url);
            $this->exist = false;
            $this->stream = '';
        }
        
        $this->stream = $html;
        $this->exist = true;
    }

    public function parse($source_id)
    {
        $source_name = Source::getShortDescription($source_id);
        $source_manager = new $source_name();
        return $source_manager->specificParse($this->stream);
    }
}