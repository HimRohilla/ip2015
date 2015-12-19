<?php 

class INPUT{
    public static function exists($type = 'post'){
        switch($type){
            case 'post' : 
                return (!empty($_POST)) ? true : false;
            case 'get' :
                return (!empty($_GET)) ? true : false;
            case 'json' :
                return (!empty(json_decode(file_get_contents('php://input'), true))) ? true : false;
            default :
                return false;
        }
    }
    
    public static function get($item){
        if(isset($_POST[$item])){
            if(is_array($_POST[$item])){
                for($i=0; $i<count($_POST[$item]); $i++){
                    $_POST[$item][i] = self::sanitizeInput($_POST[$item][i]); 
                }
                return $_POST[$item];
            }
            else
                return self::sanitizeInput($_POST[$item]);
        }
        else if(isset($_GET[$item])){
            if(is_array($_GET[$item])){
                for($i=0; $i<count($_GET[$item]); $i++){
                    $_GET[$item][i] = self::sanitizeInput($_GET[$item][i]); 
                }
                return $_GET[$item];
            }
            else
                return self::sanitizeInput($_GET[$item]);
        }
        
        else {
            $data = null;
            if(!SESSION::exists('JSONInputData')){
                $temp = json_decode(file_get_contents('php://input'), true);
                if(!empty($temp)){
                    SESSION::setSession('JSONInputData',$temp);
                    $data = $temp;
                }
            }
            else{
                $data = SESSION::getSession('JSONInputData');
            }
            if($data){                                   
                if(is_array($data)){
                    for($i=0; $i<count($data[$item]); $i++){
                        $data[$item][i] = self::sanitizeInput($data[$item][i]);
                    }
                    return $data[$item];
                }
                else
                    return self::sanitizeInput($data[$item]);
            }
        }
        throw new CustomException("$item is not a request parameter.");
    }
    
    public static function getToken($tokenKey = "csrf_token"){
        if(!is_string($tokenKey) || $tokenKey == ""){
            throw new CustomException("CSRF token key provided is not string or an empty string");
        }
        else{
            if(!SESSION::exists($tokenKey)){
                SESSION::setSession($tokenKey, HASH::generateRandomString(20));
            }
            return SESSION::getSession($tokenKey);
        }
    }
    
    public static function isValidRequest($tokenKey = "csrf_token"){
        if(!is_string($tokenKey) || $tokenKey == ""){
            throw new CustomException("CSRF token key provided is not string or an empty string");
        }
        else{
            if(self::get($tokenKey) && Session::exists($tokenKey)){    //or access token_name from config file.
                if(self::get($tokenKey) == Session::getSession($tokenKey))
                    return true;
            }
            return false;
        }
    }
    
    private static function sanitizeInput($input){
        if(isset($input)){
            $input = trim($input);
            $input = filter_var($input, FILTER_SANITIZE_STRING);
            return $input;
        }
        return $input;
    }
}
