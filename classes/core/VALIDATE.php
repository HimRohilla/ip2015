<?php

namespace core;

namespace v1;

class VALIDATE {
    private $_passed = false,
            $_errors = array(),
            $_db = null;
    
    public function __construct(){
        $this->_db = DB::getInstance();
    }
    
    public function check($source,$items = array()){
        foreach($items as $item => $rules){
            foreach($rules as $rule => $rule_value){
                $value = trim($source[$item]);
                $item = escape($item);
                if($rule === "required" && empty($value)){
                    $this->addError("$item is required");
                }
                else if(!empty($value)){
                    switch($rule){
                        case 'min' :
                            if(!isset($rule_value)|| !is_numeric($rule_value)){
                                throw new CustomException("Rule value not set or incorrect for $rule in $item");
                            }
                            if(strlen($value) < $rule_value){
                                $this->addError("$item must be minimum of $rule_value");
                            }
                            break;

                        case 'max' :
                            if(!isset($rule_value)|| !is_numeric($rule_value)){
                                throw new CustomException("Rule value not set or incorrect for $rule in $item");
                            }
                            if(strlen($value) > $rule_value){
                                $this->addError("$item must be maximum of $rule_value");
                            }
                            break;

                        case 'matches' : 
                            if(!isset($rule_value)|| !isset($source[$rule_value])){
                                throw new CustomException("Rule value not set or incorrect for $rule in $item");
                            }
                            if($value != $source[$rule_value]){
                                $this->addError("$item must match $rule_value");
                            }
                            break;

                        case 'unique' : 
                            $check = $this->_db->get($rule_value,array($item,'=',$value));
                            if($check->count()){
                                $this->addError("$item already exists");
                            }
                            break;

                        case 'isEmail' :
                            if(!isset($rule_value)|| !in_array($rule_value, array('strict', 'loose'))){
                                throw new CustomException("Rule value not set or incorrect for $rule in $item");
                            }
                            if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                                $this->addError("$item is not a valid Email");
                                break;
                            }
                            if($rule_value == 'strict'){
                                list($userName, $mailDomain) = split("@", $value);
                                if(!checkdnsrr($mailDomain,'ANY')){
                                    $this->addError("$item is not a valid Email");
                                    break;
                                } 
                            }
                            break;

                        case 'customCheck' : 
                            if(!isset($rule_value)){
                                throw new CustomException("Rule value not set for $rule in $item");
                            }
                            if(preg_match($rule_value, null) == false){//filter_var($string, FILTER_VALIDATE_REGEXP,array("options"=>array("regexp"=>"/^M(.*)/")))
                                throw new CustomException("Not a Valid Regular Expression - $item");
                            }
                            if(preg_match($rule_value, $value) == false){
                                $this->addError("$item is not a valid input");
                            }
                            break;

                        case 'isUrl' :
                            if(!isset($rule_value)){
                                throw new CustomException("Rule value not set for $rule in $item");
                            }
                            if(preg_match('%^(?:(?:https?|ftp)://)?(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $value) == false){
                                $this->addError("$item not a valid url");
                            }
                            break;

                        case 'isTelNo' :
                            $expectedRuleValue = array(8, 10);
                            if(!isset($rule_value) || !in_array($rule_value, $expectedRuleValue, TRUE)){
                                throw new CustomException("Rule value not set OR incorrect for $rule in $item");
                            }
                            if(!in_array(strlen($value), $expectedRuleValue, TRUE) || preg_match('/^[0-9]{8,10}$/', $value) == false){
                                $this->addError("$item not a valid contact number");
                            }
                            break;

                        case 'isNumeric' : 
                            if(!is_numeric($value) || is_float(floatval($value))){
                                $this->addError("$item not numeric");
                            }
                            break;

                        case 'isAlphaNumeric' : 
                            if(!ctype_alnum($value)){
                                $this->addError("$item not numeric");
                            }
                            break;

                        case 'checkUploadedFile':
                            if($source != '$_FILES'){
                                throw new CustomException("$source is not a correct source to validate uploaded files(should be '$_FILES')");
                            }
                            if(!isset($rule_value) || !is_array($rule_value)){
                                 throw new CustomException("Rule value not set OR not an array for $rule in $item");
                            }

                            $allowedExt = array('jpg','jpeg','xlsx','xls');
                            $maxSize = 5000; /*in KiloByte*/
                            $mime_types_map = array('xla' => 'application/vnd.ms-excel','xlam' => 'application/vnd.ms-excel.addin.macroenabled.12','xlc' => 'application/vnd.ms-excel','xlf' => 'application/x-xliff+xml','xlm' => 'application/vnd.ms-excel','xls' => 'application/vnd.ms-excel','xlsb' => 'application/vnd.ms-excel.sheet.binary.macroenabled.12','xlsm' => 'application/vnd.ms-excel.sheet.macroenabled.12','xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','jpe' => 'image/jpeg','jpeg' => 'image/jpeg','jpg' => 'image/jpeg','gif' => 'image/gif');

                            $options = $rule_value;
                            if(!empty(options)){
                                if(isset($options['maxSize'])){
                                    if(!is_numeric($options['maxSize'])){
                                        throw new CustomException("maxSize value not numeric for $rule in $item");
                                    }
                                    $maxSize = $options['maxSize'];
                                }
                                if(isset($options['allowedExt'])){
                                    if(!is_array($options['allowedExt'])){
                                        throw new CustomException("allowedExt not array for $rule in $item");
                                    }
                                    $allowedExt = $options['allowedExt'];
                                }
                            }

                            if($source[$item]['size'] > $maxSize){
                                $this->addError("File size exceeding $maxSize bytes in $items");
                                break;
                            }
                            $fileExtension = end(explode('.',$source[$item]['name']));
                            if(!in_array($fileExtension, $allowedExt,TRUE)){
                                $this->addError("$fileExtension not allowed in $items");
                                break;
                            }
                            if($source[$item]['type'] != $mime_types_map[$fileExtension]){
                                $this->addError("Corrupted file in $items");
                                break;
                            }
                            break;
                        default:
                            throw new CustomException("Not a valid rule");
                            break;
                    }
                }
            }
        }
        if(empty($this->_errors)){
            $this->_passed = true;
        }
        return $this;
    }
    
    private function addError($string){
        $this->_errors[] = $string;
    }

    public function passed(){
        return $this->_passed;
    }

    public function printErrors(){
        print_r($this->errors);
    }

    public function errors(){
        return $this->_errors;
    }
    
}