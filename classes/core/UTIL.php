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
    public static function isDate($date){
        if($date != ""){
            $array = date_parse($date);
            if(!$array['error_count'] && !$array['warning_count'])
                return true;
            else
                return false;
        }
        throw new CustomException("Argument cannot be empty");
    }
    public static function getStartEndTimestamp($monthName = "", $year = ""){
        $string = $monthName." ".$year;
        if(self::isDate($string)) {
            $numberOfDays = date("t", strtotime($string));
            $array['startTimestamp'] = strtotime($string);
            $array['endTimestamp'] = strtotime($numberOfDays + " " + $string);
            return $array;
        }
        throw new CustomException("Arguments cannot form a valid date");
    }
    
    public static function getCitiesList($stateId = "") {
        if($stateId != "") {
            $db = DB::getInstance();
            $cities = $db->traceDown("states_cities", $stateId, 1);
            $keys = array_keys($cities);
            $i = 0;
            foreach ($cities as $key => $value) {
                $city[$i]['id'] = $key;
                $city[$i++]['name'] = $value['value'];
            }
            return $city ? $city : array();
        }
        throw new CustomException("Argument cannot be empty");
    }
    
    public static function getStatesList() {
        $db = DB::getInstance();
        if($db->executeQuery(array(0), "SELECT id, name FROM states_cities WHERE parent_id IS NULL AND is_deleted = ?")->count()) {
            $state = $db->results();
            return $state ? $state : array();
        }
        else {
            return NULL;
        }
    }
    
    public static function getTeachersList($departmentId = "") {
        $db = DB::getInstance();
        if($departmentId != "") {
            if($db->executeQuery(array($departmentId), "SELECT user.uid, user.name FROM user, faculty_info WHERE faculty_info.department_id = ? AND user.uid = faculty_info.uid")->count()) {
                $teacher = $db->results();
                return $teacher ? $teacher : array();
            }
            else {
                return NULL;
            }
        }
        else {
            throw new CustomException("Argument cannot be valid");
        }
    }
    
    public static function getLabsList() {
        $db = DB::getInstance();
        if($db->executeQuery(array(0), "SELECT id, name FROM labs_info WHERE is_deleted = ?")->count()) {
            $lab = $db->results();
            return $lab ? $lab : array();
        }
        else { 
            return NULL;
        }
    }

    public static function getShortformAndLabDetails($course_class_id = ""){
        // pending due to discussion that where group field is to be placed
        // in class_teacher or in any other table
    }
    
    /*
     * 
     * *****************************************************************************
     * below functions made after the changes in department_course_subject table
     * *****************************************************************************
     * 
     */
    
    public static function getClassesList($departmentId) {
        // classes name and id for all department that are active for that session
    }
    
    public static function getDepartmentsList() {
        
    }
    
    public static function getSubjectList($departmentId = ""){
        if($departmentId != "") {
            $db = DB::getInstance();
            $array = $db->traceDown("department_course_subject", $departmentId, 2);
            return $array;
        }
        throw new CustomException("Argument cannot be empty");
    }    
}
