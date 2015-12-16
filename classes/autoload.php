<?php
function library_autoload($classname){
    if(file_exists(__DIR__."/core/$classname.php")){
        require_once __DIR__."/core/$classname.php";
        return true;
    }
    else if(file_exists(__DIR__."/extended/$classname.php")){
        require_once __DIR__."/extended/$classname.php";
        return true;
    }
    return false;
}

spl_autoload_register('library_autoload');