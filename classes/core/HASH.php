<?php

namespace core;

namespace v1;

require_once 'includes/UUID.php';

class HASH {
    
    const ALPHANUMERIC = array(
        'characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    );
    
    const NUMERIC = array(
        'characters' => '0123456789'
    );
    
    const ALPHABETIC = array(
        'characters' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    );
    
    private static $_passwordHashOptions = array(
        'cost' => 10
    );
    
    public static function generateRandomUUID(){
        return UUID::generate(4);
    }
    
    public static function generatePassword($password = ""){
        if($password == ""){
           throw new CustomException("Password string not provided"); 
        }
        else{
            $hash = password_hash($password,PASSWORD_DEFAULT,self::$_passwordHashOptions);
            if($hash == ""){
                throw new CustomException("Sorry, the password wasn't hashed due to some reason");
            }
            else{
                return $hash;
            }
        }
    }
    
    public static function verifyPassword($passwordToBeMatched = "",$hash = ""){
        if($passwordToBeMatched == ""){
            throw new CustomException("Password to be matched not provided.");
        }
        else if($hash == ""){
            throw new CustomException("Hash to be matched not provided.");
        }
        else{
            return password_verify($passwordToBeMatched, $hash);
        }
    }
    
    /*
     * This is used for generation random strings for any purposes in the system.
     * Like passwords, verification strings , etc.
     * Strings can be duplicate
     * Defaults to alphanumeric
     */
    
    public static function generateRandomString($length = 8,$stringType = self::ALPHANUMERIC){
        if($length <= 0){
            throw new CustomException("Length of random string cannot be zero.");
        }
        else if($stringType != self::ALPHANUMERIC && $stringType != self::ALPHABETIC && $stringType != self::NUMERIC){
            throw new CustomException("Provide a valid a string type for the random string generation.");
        }
        else{
            echo "Length is $length";
            $characters = $stringType['characters'];
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
    }
    
}
