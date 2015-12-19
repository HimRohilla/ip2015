<?php

class UTIL {
    
    const NOT_INCLUDE = array(".","..");
    
    public static function getFilesInDirectory($directory = ""){
        if(!is_string($directory) || $directory == ""){
            throw new CustomException("Directory name is not string or an empty string");
        }
        else{
            $list = scandir($directory);
            return array_diff($list, self::NOT_INCLUDE);
        }
    }
    
    public static function recursiveFileExists($filename = "", $directory = "")
    {
        if(!is_string($filename) || $filename == ""){
            throw new CustomException("File name is not string or an empty string");
        }
        else if(!is_string($directory) || $directory == ""){
            throw new CustomException("Directory name is not string or an empty string");
        }
        else{
            try
            {
                /*** loop through the files in directory ***/
                foreach(new recursiveIteratorIterator( new recursiveDirectoryIterator($directory)) as $file)
                {
                    /*** if the file is found ***/
                    if( $directory.'/'.$filename == $file )
                    {
                        return true;
                    }
                }
                /*** if the file is not found ***/
                return false;
            }
            catch(Exception $e)
            {
                /*** if the directory does not exist or the directory
                    or a sub directory does not have sufficent
                    permissions return false ***/
                return false;
            }
        }
    }
    
    public static function array_flatten($array,$return = array()) 
    {
        if(!is_array($array) || $array == array()){
            throw new CustomException("Array parameter to be flattened is not an array or an empty array");
        }
        else if(!is_array($return)){
            throw new CustomException("Recursive array parameter is not array");
        }
        foreach($array as $itemIndex => $itemValue) {
            if(is_array($array[$itemIndex])) {
                    $return = self::array_flatten($array[$itemIndex], $return);
            }
            else {
                    if(isset($array[$itemIndex])) {
                            $return[] = $array[$itemIndex];
                    }
            }
        }
        return $return;
    }
}
