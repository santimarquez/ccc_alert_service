<?php

class Html
{
    public $exist;
    public $stream;

    function __construct($url = NULL)
    {
        if ($url === NULL) {
            $this->stream = '';
            $this->exist = false;
        }
    }

    /**
     * Generate and execute de cURL request
     * to get the HTML stream.
     * Store the content in the property stream
     * Fill the exist switch.
     *
     * @param [string] $url
     * @return void
     */
    public function retrieve($url)
    {
        // Retrieve the standard HTML parsing array for later use.
        $htmloptions = TagFilter::GetHTMLOptions();
        $web = new WebBrowser();
        $result = $web->Process($url);

        // Capture the stream
        $html = $result["body"];

        if (!$html) {
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
