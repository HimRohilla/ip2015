<?php	

class Time{
        private static function isEmpty($timestamp){
                if(!$timestamp)
                        $timestamp = time();
                return $timestamp;			
        }
        public static function getDate($timestamp){
                try{
                        if(is_numeric($timestamp))
                                return date('D d M, Y', self::isEmpty($timestamp));
                        throw new CustomException("Argument type must be an 'integer'");
                }
                catch(CustomException $e){
                        echo $e->printStackTrace();
                        return null;
                }
        }
        public static function getDay($timestamp){
                try{
                        if(is_numeric($timestamp))
                                return date('l', self::isEmpty($timestamp));
                        throw new CustomException("Argument type must be an 'integer'");
                }
                catch(CustomException $e){
                        echo $e->printStackTrace();
                        return null;
                }
        }
        public static function getTime($timestamp){
                try {
                        date_default_timezone_set("Asia/Kolkata");
                        if(is_numeric($timestamp))
                                return date('h:i A', self::isEmpty($timestamp));
                        throw new CustomException("Argument type must be an 'integer'");
                }
                catch(CustomException $e){
                        echo $e->printStackTrace();
                        return null;
                }
        }
        public static function getFullDateTime($timestamp){
                try {
                        if(is_numeric($timestamp))
                                return self::getDate(self::isEmpty($timestamp)).' '.self::getTime(self::isEmpty($timestamp));
                        throw new CustomException("Argument type must be an 'integer'");
                }
                catch(CustomException $e){
                        echo $e->printStackTrace();
                        return null;
                }
        }
        public static function changeDateFormat($date, $format='D d M, Y'){
                try {
                        $dateArray = date_parse($date);
                        if(checkdate($dateArray['month'], $dateArray['day'], $dateArray['year'])){
                                $timestamp = strtotime($date);
                                return date($format, $timestamp);
                        }
                        throw new CustomException("Date Format is not valid");
                }
                catch(CustomException $e){
                        echo $e->printStackTrace();
                        return null;
                }
        }
        public static function getTimestamp($date){
                try {
                        if(checkdate($dateArray['month'], $dateArray['day'], $dateArray['year']))
                                return strtotime($date);
                        throw new CustomException("Date format is not valid");
                }
                catch(CustomException $e){
                        echo $e->printStackTrace();
                        return null;
                }
        }
}