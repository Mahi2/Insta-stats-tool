<?php

class InstagramHelper {

    public static function get_hashtags($string) {

        preg_match_all('/#([\w\d]+)/', $string, $array);


        return $array[1];

    }

    public static function get_mentions($string) {

        preg_match_all('/@([\w\d][\w\d\.\_]+[\w\d])/', $string, $array);


        return $array[1];

    }


    public static function get_embed_html($shortcode) {

        $url = 'https://api.instagram.com/oembed/?url=http://instagr.am/p/' . $shortcode . '/&hidecaption=true&maxwidth=450';

        /* Initiate curl */
        $ch = curl_init();

        /* Disable SSL verification */
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        /* Will return the response */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /* Set the Url */
        curl_setopt($ch, CURLOPT_URL, $url);

        /* Execute */
        $data = curl_exec($ch);

        /* Close */
        curl_close($ch);

        $response = json_decode($data);

        return $response ? $response->html : false;

    }

}
