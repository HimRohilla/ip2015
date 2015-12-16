<?php

require_once '../core/autoload.php';

class Student extends User{
    
    public function __construct($userId) {
        parent::__construct($userId);
        
    }
}
