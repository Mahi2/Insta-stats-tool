<?php
use Unirest\Request;

class YouTube {

    private $api_url = 'https://www.googleapis.com/youtube/v3/';
    private $type = '';
    private $parameters = [];

    /* This needs to be set to the actual website url */
    private $header_referer = '';

    public function __construct() {

        if(!function_exists('curl_version')) {

            throw new Exception('Your webhost does not support curl and we cannot continue with the request.');

        }

    }

    public function set_type($type) {
        $this->type = $type;
    }

    public function set_parameters(Array $parameters) {
        foreach($parameters as $key => $value) {

            /* Check for parameters that need to be unset */
            if(($value == '' || !$value) && array_key_exists($key, $parameters)) {
                unset($this->parameters[$key]);
            } else {
                $this->parameters[$key] = $value;
            }

        }
    }

    public function set_header_referer($referer) {
        $this->header_referer = $referer;
    }

    /* Function to get the content of the requested url with a specific function */
    public function get() {
        $parameters = [];

        foreach($this->parameters as $key => $value) {
            $parameters[] = $key . '=' . $value;
        }

        $parameters = implode('&', $parameters);

        $url = $this->api_url . $this->type . '?' . $parameters;

        $response = Request::get($url, ['Referer' => $this->header_referer]);

        return $this->parse($response->body);
    }


    public function parse($body) {

        if(count($body->items) > 0) {
            return $body->items;
        } else {
            return false;
        }


    }

}
