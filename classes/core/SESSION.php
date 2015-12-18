<?php

class SESSION {
        private static $salt = array(9, 5, 8, 2, 4, 5, 7, 8, 1, 3);
        private static $salt_string = "";

        public static function init_session(){
                session_start();
                session_regenerate_id();
        }
        private static function is_json($value){
                json_decode($value);
                return (json_last_error() == JSON_ERROR_NONE);
        }		
        private static function getHashed($key){
                $salt_string = self::$salt_string;
                $newKey = hash_hmac("md5", $key, $salt_string);
                return $newKey;
        }
        public static function exists($key){
                return (isset($_SESSION[self::getHashed($key)]));
        }
        public static function getSession($key){
                try{
                        if(self::exists($key)){
                                $value = $_SESSION[self::getHashed($key)];
                                if(self::is_json($value))
                                        return json_decode($value,true);
                                else
                                        return self::decode($value,true);
                        }
                        throw new CustomException("Session <b>".$key."</b> does not exists");
                }
                catch(CustomException $e){
                        echo $e->printStackTrace();
                        return null;
                }
        }
        public static function deleteSession($key){
                try {
                        if(self::exists($key)){
                            unset($_SESSION[self::getHashed($key)]);
                            return true;
                        }
                        throw new CustomException("Session <b>".$key."</b> does not exists");
                }
                catch(CustomException $e){
                        echo $e->printStackTrace();
                        return false;
                }

        }
        public static function setSession($key, $value){
                try {
                        if(is_string($value) || is_numeric($value))
                                $value = self::encode($value);
                        else
                                $value = json_encode($value);
                        $_SESSION[self::getHashed($key)] = $value;
                        return self::exists($key);
                        throw new CustomException("Session <b>".$key."</b> cannot be set");
                }
                catch(CustomException $e){
                        echo $e->printStackTrace();
                        return false;
                }
        }

        public static function setFlash($key,$value){
            if(!is_string($value) && !is_numeric($value)){
                throw new CustomException("Flash messages can only be interer or string");
            }
            self::setSession($key, $value);
        }

        public static function flash($key){
            $flashMessage = "";
            if(self::exists($key) && is_string(self::getSession($key))){
                $flashMessage = self::getSession($key);
                self::deleteSession($key);
            }
            return $flashMessage;
        }

        private static function encode($value){
                $newValue = "";
                $salt = self::$salt;
                for ($i=0; $i <strlen($value) ; $i++) { 
                        $ascii = ord($value[$i]);
                        if(isset($salt[$i]))
                                $ind = $i;
                        else
                                $ind = $i - 10;

                        $ascii += $salt[$ind];
                        $newValue .= chr($ascii);
                }
                return base64_encode(strrev($newValue));
        }
        private static function decode($value){
                $value = strrev(base64_decode($value));
                $newValue = "";
                $salt = self::$salt;
                for ($i=0; $i <strlen($value) ; $i++) { 
                        $ascii = ord($value[$i]);
                        if(isset($salt[$i]))
                                $ind = $i;
                        else
                                $ind = $i - 10;

                        $ascii -= $salt[$ind];
                        $newValue .= chr($ascii);				
                }
                return $newValue;
        }		
}
?>