<?php

class USER {
    
    protected $_db = null;
    
    protected $_userId = null;
    
    protected $_permissions = null;
    
    public function __construct($userId) {
        if(!$userId){
            throw new CustomException("User ID Not Found");
        }
        else{
            $this->_db = DB::getInstance();
            if(!$this->_db->select("user",array("UID" => $userId,"is_deleted" => 0),array("UID"))->count()){
                throw new CustomException("No User Found Prior To The User ID");
            }
            else{
                $this->_userId = $this->_db->results()[0]["UID"];
                $this->fetchUserTypePermissions();
            }
        }
    }
    
    private function fetchUserTypePermissions(){
        if($this->_db->select("user_usertype",array("user_id" => $this->_userId,"is_deleted" => 0),array("usertype_id"))->count()){
            $userTypeSet = "";
            $clauseSet = array();
            $results = $this->_db->results();
            foreach($results as $result){
                $userTypeSet .= "?,";
                $clauseSet[] = $result["usertype_id"];
            }
            $clauseSet[] = $this->_userId;
            $clauseSet[] = 0;
            $clauseSet[] = 0;
            $temp = $clauseSet;
            foreach($temp as $clause){
                array_push($clauseSet, $clause);
            }
            $userTypeSet = "(".rtrim($userTypeSet,",").")";
            if($this->_db->executeQuery($clauseSet,"SELECT permission_id FROM primary_usertype_permission WHERE usertype_id IN $userTypeSet AND permission_id NOT IN (SELECT permission_id FROM user_permission_revoked WHERE user_id=? AND is_deleted=?) AND is_deleted=? UNION SELECT permission_id FROM secondary_usertype_permission WHERE usertype_id IN  $userTypeSet AND permission_id NOT IN (SELECT permission_id FROM user_permission_revoked WHERE user_id=? AND is_deleted=?) AND is_deleted=?")->count()){
                $results = UTIL::array_flatten($this->_db->results(),array());
                $clauseString = "(";
                foreach($results as $item){
                    $clauseString .= "?,";
                }
                $clauseString = rtrim($clauseString,",").")";
                $this->_permissions = UTIL::array_flatten($this->_db->executeQuery($results,"SELECT name FROM permission WHERE id IN $clauseString")->results(),array());
            }
        }
    }
    
    public function getPermissionList(){
        return ($this->_permissions) ? $this->_permissions : array();
    }
    
    public function getUserId(){
        return ($this->_userId) ? $this->_userId : "";
    }
    
}