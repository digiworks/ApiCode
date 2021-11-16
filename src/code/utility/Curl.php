<?php

namespace code\utility;

use code\utility\string\Str;

class Curl {

    public static $timeout = 10;
    public static $useragent = 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';
    /**
     * 
     * @param type $url
     * @param array $get
     * @param array $options
     * @return type
     */
    public static function get($url, array $get = NULL, array $options = array())
    {
        $result = "";
        if(Str::startsWith($url,"http",false))
        {
            $result = static::curl_get($url,$get,$options);
        }else{
            $result = file_get_contents($url);
        }
        return $result;
    }
    
    /**
     * 
     * @param type $url
     * @param array $get
     * @param array $options
     * @return type
     */
    protected static function curl_get($url, array $get = NULL, array $options = array()) {
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; // browsers keep this blank.
        $defaults = array(
            CURLOPT_URL => $url . (strpos($url, '?') === FALSE && !is_null($get) ? '?' . http_build_query($get) : '') ,
            CURLOPT_HEADER => 0,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_USERAGENT => static::$useragent,
            CURLOPT_TIMEOUT => static::$timeout,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if (!$result = curl_exec($ch)) {
            
            trigger_error($url . "-->" . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

}
