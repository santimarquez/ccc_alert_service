<?php

Class Url
{
    public $url;
    public $protocol;
    public $host;
    public $path;
    public $query;

    function __construct($url)
    {
        $this->url = $url;
        $this->protocol = parse_url($url, PHP_URL_SCHEME);
        $this->host = parse_url($url, PHP_URL_HOST);
        $this->path = parse_url($url, PHP_URL_PATH);
        $this->query = parse_url($url, PHP_URL_QUERY);
    }

    public function append($get_var_list)
    {
        if($this->query === NULL)
        {
            $get_string = '?';
        }
        
        while($get_var = $get_var_list->fetch_object()) {
            $get_string .= $get_var->get_variable . '=' . $get_var->value . '&';
        }

        $this->url .= rtrim($get_string, '&');
    }
}