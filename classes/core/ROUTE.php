<?php

class ROUTE {
    
    private static $_status = array(
                    100 => 'Continue',  
                    101 => 'Switching Protocols',  
                    200 => 'OK',
                    201 => 'Created',  
                    202 => 'Accepted',  
                    203 => 'Non-Authoritative Information',  
                    204 => 'No Content',  
                    205 => 'Reset Content',  
                    206 => 'Partial Content',  
                    300 => 'Multiple Choices',  
                    301 => 'Moved Permanently',  
                    302 => 'Found',  
                    303 => 'See Other',  
                    304 => 'Not Modified',  
                    305 => 'Use Proxy',  
                    306 => '(Unused)',  
                    307 => 'Temporary Redirect',  
                    400 => 'Bad Request',  
                    401 => 'Unauthorized',  
                    402 => 'Payment Required',  
                    403 => 'Forbidden',  
                    404 => 'Not Found',  
                    405 => 'Method Not Allowed',  
                    406 => 'Not Acceptable',  
                    407 => 'Proxy Authentication Required',  
                    408 => 'Request Timeout',  
                    409 => 'Conflict',  
                    410 => 'Gone',  
                    411 => 'Length Required',  
                    412 => 'Precondition Failed',  
                    413 => 'Request Entity Too Large',  
                    414 => 'Request-URI Too Long',  
                    415 => 'Unsupported Media Type',  
                    416 => 'Requested Range Not Satisfiable',  
                    417 => 'Expectation Failed',  
                    500 => 'Internal Server Error',  
                    501 => 'Not Implemented',  
                    502 => 'Bad Gateway',  
                    503 => 'Service Unavailable',  
                    504 => 'Gateway Timeout',  
                    505 => 'HTTP Version Not Supported'
        );
    
    private static $_filterRedirectURL = "";
    
    
    public static function to($path){
        if(is_string($path) && $path != ""){
            header("Location:$path");
        }
        else if(is_int($path) && $path > 100 && $path < 505 && in_array($path,  array_keys(self::$_status))){
            header("HTTP/1.0 $path ".self::$_status[$path]);
            require_once(__DIR__."/../../errors/$path.php");
        }
        else{
            throw new CustomException("There is some problem in your provided path. Please check the path provided");
        }
    }
    
    public static function getAbsoluteURL($screenName){
        $screenName = strval($screenName);
        if(!$screenName){
            throw new CustomException("Screen Name Not Provided");
        }
        else if(!isset(SESSION::getSession("sitemap")[$screenName])){
            throw new CustomException("No Such Screen Exists. Please check the name again");
        }
        else{
            return $GLOBALS["CONFIG"]["URL"].SESSION::getSession("sitemap")[$screenName];
        }
    }
    
    public static function getRelativeURL($screenName){
        $screenName = strval($screenName);
        if(!$screenName){
            throw new CustomException("Screen Name Not Provided");
        }
        else if(!isset(SESSION::getSession("sitemap")[$screenName])){
            throw new CustomException("No Such Screen Exists. Please check the name again");
        }
        else{
            return SESSION::getSession("sitemap")[$screenName];
        }
    }
    
    public static function setFilterRedirect($screenName = ""){
        if(!is_string($screenName)){
            throw new CustomException("Please provide a valid string for screen name");
        }
        else if(!isset(SESSION::getSession("sitemap")[$screenName])){
            throw new CustomException("No Such Screen Exists. Please check the name again");
        }
        else{
            self::$_filterRedirectURL = SESSION::getSession("sitemap")[$screenName];
        }
    }
   
    public static function isFilterRedirect(){
        return (self::$_filterRedirectURL == "") ? false : true;
    }
    
    public static function getFilterRedirect(){
        return self::$_filterRedirectURL; 
    }
}
