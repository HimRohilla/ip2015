<?php

namespace core;

namespace v1; 

class AUTH {
  
    public static function login($username = "",$password = "",$usernameType = "email"){
       if(is_string($username) && is_string($password) && is_string($usernameType)){
            $db = DB::getInstance();
            if($db->select("user",array($usernameType => $username,"is_deleted" => 0),array("UID","name","password"))->count()){
                $result = $db->results();
                $passwordHash = $result[0]["password"];
                $userId = $result[0]["UID"];
                $name = $result[0]["name"];
                if(HASH::verifyPassword($password,$passwordHash)){
                    $user = new User($userId);
                    $userSet = array(
                        "user-id" => $userId,
                        "name" => $name,
                        "permissions" => $user->getPermissionList()
                    );
                    if(SESSION::exists("user")){
                        SESSION::deleteSession("user");
                    }
                    SESSION::setSession("user",$userSet);
                    return true;
                }
            }
            return false;
       }
       else{
           throw new CustomException("Either of the credential parameter is not string type");
       }
    }
    
    public static function logout(){
        if(SESSION::exists("user")){
           SESSION::deleteSession("user");
        }
    }
    
    public static function isAuthorized($url = ""){
        if(!$url){
            throw new CustomException("No url is provided");
        }
        else if(!SESSION::exists("user")){
            throw new CustomException("No logged in user exists");
        }
        else{
            $siteMap = array_keys(SESSION::getSession("sitemap"));
            $key = array_search($url,SESSION::getSession("sitemap"));
            if(in_array($key,$siteMap)){
                return true;
            }
        }
        return false;
    }
    /**
     * Returns the logged in user details
     * @return Array
     */
    public static function getUser(){
        if(!Session::exists("user")){
            throw new CustomException("no user is logged in");
        }
        return Session::getSession("user");
    }
    
    public static function isLoggedIn(){
        return SESSION::exists("user") ? true : false;
    }
}
