<?php

namespace core;

namespace v1;

class UTIL {
    
    const NOT_INCLUDE = array(".","..");
    
    public static function getFilesInDirectory($directory){
        $directory = strval($directory);
        if(!$directory){
            throw new Exception("Please provide a valid directory name");
        }
        else if(!is_dir($directory)){
            throw new Exception("No such directory exists");
        }
        else{
            $list = scandir($directory);
            return array_diff($list, self::NOT_INCLUDE);
        }
    }
    
    public static function recursive_file_exists($filename, $directory)
    {
        $filename = strval($filename);
        $directory = strval($directory);
        if(!$filename){
            throw new Exception("File Name not provided");
        }
        else if(!$directory){
            throw new Exception("Directory Name not provided");
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
    
    public static function array_flatten($array,$return) {
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
