<?php
use Unirest\Request;

class Facebook {

    private $api_url = 'https://www.facebook.com/';
    private $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36';


    public function __construct() {

        if(!function_exists('curl_version')) {

            throw new Exception('Your webhost does not support curl and we cannot continue with the request.');

        }

    }

    public function set_user_agent($user_agent) {
        return $this->user_agent = $user_agent;
    }

    public static function set_proxy(array $config) {
        $default_config = [
            'port' => false,
            'tunnel' => false,
            'address' => false,
            'type' => CURLPROXY_HTTP,
            'timeout' => false,
            'auth' => [
                'user' => '',
                'pass' => '',
                'method' => CURLAUTH_BASIC
            ]
        ];

        $config = array_replace($default_config, $config);

        Request::proxy($config['address'], $config['port'], $config['type'], $config['tunnel']);

        if(isset($config['auth'])) {
            Request::proxyAuth($config['auth']['user'], $config['auth']['pass'], $config['auth']['method']);
        }

        if(isset($config['timeout'])) {
            Request::timeout((int) $config['timeout']);
        }
    }

    public function generate_headers() {
        $headers = [
//            'cache-control'             => 'max-age=0',
//            'upgrade-insecure-requests' => '1',
//            'accept'                    => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8',
//            'accept-encoding'           => 'gzip, deflate, br',
            'accept-language'           => 'en-US,en;q=0.9,fr;q=0.8,ro;q=0.7,ru;q=0.6,la;q=0.5,pt;q=0.4,de;q=0.3'
        ];

        $headers['user-agent'] = $this->user_agent;

        return $headers;
    }

    /* Function to get the content of the requested url with a specific function */
    public function get($path) {

        $url = $this->api_url . $path;

        $response = Request::get($url, $this->generate_headers());

        return $this->parse($response->raw_body);
    }

    public function get_meta_content($content, $property) {

        preg_match('/<meta property="' . $property . '" content="([^"]+)"/', $content, $match, PREG_OFFSET_CAPTURE);

        if($match && count($match) > 1) {

            return $match[1][0];

        } else {

            throw new Exception('We could not get all the details about the page properly.');

        }


    }

    public function parse($raw_body) {

        $response = new StdClass();

        $response->name = $this->get_meta_content($raw_body, 'og:title');


        if($response->name == 'Log In or Sign Up to View') {
            throw new Exception('Account with given username does not exist or it is not an Offical Page.', 404);
        }


        $response->profile_picture_url = $this->get_meta_content($raw_body, 'og:image');
        $response->profile_picture_url = str_replace('amp;', '', $response->profile_picture_url);

        /* Default for now */
        $response->followers = 0;

        /* Start parsing the html in another way */
        $parsed = str_get_html($raw_body);

        /* Likes parsing */
        $likes_text = $parsed->find('#PagesLikesCountDOMID', 0)->plaintext;
        preg_match('/([\d,\.]+)/', $likes_text, $match, PREG_OFFSET_CAPTURE);

        if(count($match) > 0) {
            $response->likes = (int) str_replace(',', '', $match[0][0]);
        } else {
            $response->likes = 0;
        }

        /* Follow this parsing */
        $followers_text = $parsed->find('#pages_side_column > div > div > div:nth-child(3) > div > div:nth-child(2) > div:nth-child(3) > div > div:nth-child(2) > div', 1)->plaintext;
        preg_match('/([\d,\.]+)/', $followers_text, $match, PREG_OFFSET_CAPTURE);


        if(count($match) > 0) {
            $response->followers = (int) str_replace(',', '', $match[0][0]);
        } else {
            $response->followers = 0;
        }

        $response->type = false;

        /* Check if the account is verified */
        $response->is_verified = strpos($raw_body, 'data-hovercard-prefer-more-content-show=\"1\" href=\"#\" role=\"button\"') !== false;

        return $response;
    }

}
