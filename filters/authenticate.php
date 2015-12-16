<?php 

try{
    if(!SESSION::exists("user")){
        if(INPUT::get("username") && INPUT::get("password") && INPUT::get("csrf_token")){
            if(INPUT::isValidRequest()){
                if(AUTH::login(INPUT::get("username"),INPUT::get("password"))){
                  $filterSet = json_decode(file_get_contents(__DIR__."/filters.json"),true);
                }
                else{
                    SESSION::setFlash("error","Username or password is incorrect");
                    header("Location:".ROUTE::getAbsoluteURL("Login"));
                }
            }
        }
        else{
            Session::setFlash("error", "No username and password provided");
            header("Location:".ROUTE::getAbsoluteURL("Login"));
        }
    }
    if(AUTH::isLoggedIn()){
        foreach($filterSet as $filter){
            if(!ROUTE::isFilterRedirect()){
                $filename = "{$filter['filter-file']}";
                $decidingHandler = "{$filter['deciding-handler']}";
                if(ROUTE::getRelativeURL($decidingHandler) == $url){
                   if(file_exists(__DIR__."/../private/".ROUTE::getRelativeURL($decidingHandler))){
                        require_once __DIR__."/../private/".ROUTE::getRelativeURL($decidingHandler);
                   }
                   else if(file_exists(__DIR__."/../public/".ROUTE::getRelativeURL($decidingHandler))){
                        require_once __DIR__."/../public/".ROUTE::getRelativeURL($decidingHandler);
                   }
                   exit();
                }
                else{
                    if(file_exists(__DIR__."/$filename")){
                        require_once __DIR__."/$filename";
                    }
                    continue;
                }
            }
            else{
                break;
            }
        } 

        if(ROUTE::isFilterRedirect()){
            $redirectFile = ROUTE::getFilterRedirect();
            if(file_exists(__DIR__."/../private/".$redirectFile)){
                require_once __DIR__."/../private/".$redirectFile;
            }
            else if(file_exists(__DIR__."/../public/".$redirectFile)){
                require_once __DIR__."/../public/".$redirectFile;
            }
            else{
                echo "<br>404";
            }
            exit();
        }
        if(AUTH::isAuthorized($url)){
            require_once __DIR__."/../private/$url";
        }
        else{
            header("HTTP/1.0 403 Forbidden");
            header(__DIR__."/../errors/403.php");
        }
    }

    
} catch (CustomException $ex) {
    echo $ex->printStackTrace();
}