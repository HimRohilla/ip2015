<?php 
class INPUT{
	
    public static function exists($type = 'post'){
        switch($type){
            case 'post' : 
                return (!empty($_POST)) ? true : false;
            case 'get' :
                return (!empty($_GET)) ? true : false;
            default :
                return false;
        }
    }
    
    public static function getToken(){
        if(!SESSION::exists('csrf_token')){
           SESSION::setSession("csrf_token",HASH::generateRandomString(20));
        }
        return SESSION::getSession("csrf_token"); 
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
        return '';
    }
    
    public static function isValidRequest(){
        if(INPUT::get('csrf_token') && SESSION::exists('csrf_token')){    //or access token_name from config file.
            if(Input::get('csrf_token') == SESSION::getSession('csrf_token'))
                SESSION::deleteSession ("csrf_token");
                return true;
        }
        SESSION::deleteSession("csrf_token"); 
        return false;
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

/*


//use trim() before applying this shit
FILTER_SANITIZE_NUMBER_INT  =>  for numbers only
FILTER_VALIDATE_EMAIL   =>  for email

FILTER_SANITIZE_STRING  =>  mother of all filters
//FILTER_SANITIZE_URL     =>url               /*DONT USE THIS - THIS MOTHERFUCKER NOT USEFUL*/


/*

IDEA :=     Using sanitizing in validation if validation is successful?    - BTa HIMACHU and LAkSHAY BRO
*/


