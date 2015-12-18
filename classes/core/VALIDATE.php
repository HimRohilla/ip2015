<?php
class VALIDATE {
    private $_passed = false,
            $_errors = array(),
            $_db = null;
    
    public function __construct(){
        $this->_db = DB::getInstance();
    }
    /*  check():
            "$source" expected value => '$_POST', '$_GET', 'JSON', '$_FILES'
            "$items" => should be array;
            returns - Validate class Object
    */
    public function check($source,$items = array()){
        if(!isset($source)|| !in_array($source, array('$_POST', '$_GET','JSON','$_FILES'))){
            throw new CustomException("Source value not set or incorrect.");
        }
        if($source == 'JSON'){
            $source = json_decode(file_get_contents('php://input'),true);
        }
        foreach($items as $item => $rules){
            $item = trim($item);

            if(isset($rules['multiple']) && $rules['multiple'] === true){
                if(!is_array($source[$item])){
                    throw new CustomException("$item should be array - as multiple rule used");
                }
                unset($rules['multiple']);

                foreach ($source[$item] as $value) {
                    $value = INPUT::sanitizeInput($value); 
                    $this->validate($source, $item, $rules, $value);                
                }
            }
            else{
                if(is_array($source[$item])){
                    throw new CustomException("$item should not be array - as multiple rule not specified");
                }
                $value = INPUT::sanitizeInput($source[$item]); 
                $this->validate($source, $item, $rules, $value);
            }
        }
        if(empty($this->_errors)){
            $this->_passed = true;
        }
        return $this;
    }
    
    private function validate($source, $item, $rules, $value){
        foreach($rules as $rule => $rule_value){
            if($rule === "required" && $rule_value === true){
                if(empty($value))
                    $this->addError("$item is required");
            }
            else if(!empty($value)){
                switch($rule){
                    case 'min' :
                        if(!isset($rule_value)|| !is_numeric($rule_value)){
                            throw new CustomException("Rule value not set or incorrect for $rule in $item");
                        }
                        if(strlen($value) < $rule_value){
                            $this->addError("$item must be less than $rule_value");
                        }
                        break;

                    case 'max' :
                        if(!isset($rule_value)|| !is_numeric($rule_value)){
                            throw new CustomException("Rule value not set or incorrect for $rule in $item");
                        }
                        if(strlen($value) > $rule_value){
                            $this->addError("$item must be greater than $rule_value");
                        }
                        break;

                    case 'matches' : 
                        if(!isset($rule_value)|| !isset($source[$rule_value])){
                            throw new CustomException("Rule value not set or incorrect for $rule in $item");
                        }
                        if($rule_value == $item){
                            throw new CustomException("Rule_value cannot be same as parameter value for $rule in $item");
                        }
                        if($value != $source[$rule_value]){
                            $this->addError("$item must match $rule_value");
                        }
                        break;

                    case 'unique' : 
                        if(!is_array($rule_value)|| (!isset($rule_value['tableName'])&&!isset($rule_value['fieldName']))){
                            throw new CustomException("Rule value not set or incorrect for $rule in $item");
                        }
                        $check = $this->_db->select($rule_value['tableName'],array($rule_value['fieldName']=>$value),array($rule_value['fieldName']),'');
                        if($check->count()){
                            $this->addError("$item already exists");
                        }
                        break;

                    case 'isEmail' :
                        if(!isset($rule_value)|| !in_array($rule_value, array('strict', 'loose'))){
                            throw new CustomException("Rule value not set or incorrect for $rule in $item");
                        }
                        if(!filter_var($value, FILTER_VALIDATE_EMAIL)){
                            $this->addError("$item ($value) is not a valid Email");
                            break;
                        }
                        if($rule_value == 'strict'){
                            $SMTPValidator = new SMTP_validateEmail();
                            // turn on debugging if you want to view the SMTP transaction
                            //$SMTP_Validator->debug = true;
                            $results = $SMTPValidator->validate(array($email));
                            if (!$results[$email]) {
                                list($userName, $mailDomain) = split("@", $value);
                                if (!checkdnsrr($mailDomain, 'ANY')) {
                                    $this->addError("$item ($value) is not a valid Email");
                                }
                            } 
                        }
                        break;

                    case 'customCheck' : 
                        if(!isset($rule_value)){
                            throw new CustomException("Rule value not set for $rule in $item");
                        }
                        if(preg_match($rule_value, null) == false){
                            throw new CustomException("Not a Valid Regular Expression - $item");
                        }
                        if(preg_match($rule_value, $value) == false){
                            $this->addError("$item is not a valid input");
                        }
                        break;

                    case 'isUrl' :
                        if(!isset($rule_value) || $rule_value !== true){
                            throw new CustomException("Rule value not set OR incorrect for $rule in $item");
                        }
                        if(preg_match('%^(?:(?:https?|ftp)://)?(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $value) == false){
                            $this->addError("$item not a valid url");
                        }
                        break;

                    case 'isTelNo' :
                        $expectedRuleValue = array(8, 10);
                        if(!isset($rule_value) || $rule_value !== true){ 
                            throw new CustomException("Rule value not set OR incorrect for $rule in $item");
                        }
                        if(!in_array(strlen($value), $expectedRuleValue, TRUE) || preg_match('/^[0-9]{8,10}$/', $value) == false){
                            $this->addError("$item not a valid contact number");
                        }
                        break;

                    case 'isNumeric' : 
                        if(!isset($rule_value) || $rule_value !== true){
                            throw new CustomException("Rule value not set OR incorrect for $rule in $item");
                        }
                        if(!is_numeric($value) || is_float(floatval($value))){
                            $this->addError("$item not numeric");
                        }
                        break;

                    case 'isAlphaNumeric' :
                        if(!isset($rule_value) || $rule_value !== true){
                            throw new CustomException("Rule value not set OR incorrect for $rule in $item");
                        }
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
                            $this->addError("File size exceeding $maxSize bytes in $item");
                            break;
                        }
                        $fileExtension = end(explode('.',$source[$item]['name']));
                        if(!in_array($fileExtension, $allowedExt,TRUE)){
                            $this->addError("$fileExtension not allowed in $item");
                            break;
                        }
                        if($source[$item]['type'] != $mime_types_map[$fileExtension]){
                            $this->addError("Corrupted file in $item");
                            break;
                        }
                        break;

                    default:
                        throw new CustomException("$rule not a valid rule");
                }
            }
        }
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

/*
    Rules:-
        NOTE :- if validation rules are not applied correctly as specified , Exception is thrown accordingly with message.
        Basic Structure of rules array:-
            array(
                'parameter_name1' : (
                    'rule_name1'=> rule_value        //array of validation rules
                    'rule_name2'=> rule_value
                    ......
                ),
                'parameter_name2' : (
                    'rule_name1'=> rule_value        //array of validation rules
                    'rule_name2'=> rule_value
                    ......
                )
                .......
            );
            
            ##WARNING: You need to specify 'multiple' rule in your rule array, if your parameter_value is array. 
            
        
    'min': 
            rule_value: numeric 
            eg:  'min' => 5
            desciption: Used to validate that parameter value is not less than rule_value.
    'max':
            rule_value: numeric 
            eg:  'max' => 5
            desciption: Used to validate that parameter value is not greater than rule_value.
    'required':
            rule_value: Boolean 
            eg: 'isNumeric' =>  true (## only this value can be used)
            description: 
    'multiple':
            rule_value: Boolean 
            eg: 'isNumeric' =>  true (## only this value can be used)
            description: You need to specify this rule if parameter value is an array.
    'matches':
            rule_value: String (## parameter name should be other than this,if same name throws Exception) 
            eg:  'password' => 're_password'
            desciption: Used to validate that parameter value is equal to parameter provided as rule_value.
    'unique':
            rule_value: array('tableName'=>'sometable', 'fieldName'=>'somefield')
            eg: 'unique' => array('tableName'=>'User', 'fieldName'=>'uuid')
            description: Used to validate that parameter value is unique value(not an existing value) in specificed table of specified field.
    'isEmail':
            rule_value: String(can either be 'loose' or 'strict') (## To be used only for an email-type input)
                            'loose'=> will only check format of email
                            'strict'=> will also check whether mail domain exists or not.
            eg: 'isEmail' => 'loose' 
            description: Used to Validate that parameter value is valid Email.
    'customCheck':
            rule_value: valid Regular Expression 
            eg:- 'customCheck' => /^[0-9]*$/
            description: Used to validate that parameter value satisfies given regular expression.
    'isUrl':
            rule_value: Boolean (## To be used only for an url-type input)
            eg: 'isUrl' => true (## only this value can be used)
            description: Used to validate that parameter value is valid URL.
    'isTelNo':
            rule_value: Boolean 
            eg: 'isTelNo' => true (## only this value can be used)
            description: Used to validate that paramter value is either 8-digit phone no. or 10-digit phone no.
    'isNumeric':
            rule_value: Boolean 
            eg: 'isNumeric' =>  true (## only this value can be used)
            description: Used to validate that paramter value is numeric.
    'isAlphaNumeric':
            rule_value: Boolean 
            eg: 'isNumeric' =>  true (## only this value can be used)
            description: Used to validate that paramter value is alpha-numeric.  
    'checkUploadedFile':
            rule_value:array('maxSize' => someSize , 'allowedExt' => array('someExtension',...))
                            'maxSize' => Numeric value, file size in kilobytes, eg: 5Kb = 5000 
                            'allowedExt' => array of file extensions(STRING) eg:- '.jpeg' ,'.png'
            eg: 'checkUploadedfile' : array('maxSize' => 4000 , 'allowedExt' => array('.xls','.jpg'))
            description: Used to validate Uploaded file corresponding to provided rule_values.
                ##WARNING:- for using this rule you have to make another object of VALIDATE class and $source = '$_FILES',otherwise it would generate exception
*/