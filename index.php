<?php

require_once 'init/system_init.php';

//Proper security authentications must be done for the url;Aditya should see to it

$url = ltrim($_GET['url']);

$GLOBALS["URL"] = explode("/",$url);

if(!strstr($GLOBALS["URL"][count($GLOBALS["URL"]) - 1],".php")){
   $GLOBALS["URL"][count($GLOBALS["URL"])] = "index.php"; 
}

$url = implode("/",$GLOBALS["URL"]);

if(!Session::exists("public") && !Session::exists("private")){
    Session::setSession("public",UTIL::getFilesInDirectory("public"));
    Session::setSession("private",UTIL::getFilesInDirectory("private"));
}

if(in_array($GLOBALS["URL"][0],Session::getSession("public")) && file_exists("public/".$url)){
    require_once "public/{$url}";
}
else if(in_array($GLOBALS["URL"][0],Session::getSession("private")) && file_exists("private/".$url)){
    require_once "filters/authenticate.php";
}
else{
    ROUTE::to(404);
    exit();
}

