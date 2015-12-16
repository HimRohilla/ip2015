<?php

class DB {
   
    private static $_instance = null;
    		
    private $_pdo = null,
            $_query = null,
            $_results = null,
            $_sql = "",
            $_count = 0;
    
    private function __construct(){
        $this->_pdo = new PDO("mysql:host={$GLOBALS["CONFIG"]["DB"]["hostname"]};dbname={$GLOBALS["CONFIG"]["DB"]["dbname"]}",$GLOBALS["CONFIG"]["DB"]["username"],$GLOBALS["CONFIG"]["DB"]["password"],array(
             PDO::ATTR_PERSISTENT => true,
             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
          ));   
    }
    
    public static function getInstance(){
       if(!isset(self::$_instance)){
            self::$_instance = new DB();
       }
       return self::$_instance; 
    }
    
    public function executeQuery($queryParameters = array(),$sql = ""){
        if($sql == ""){
            $sql = $this->_sql;
        }
        $this->_query = $this->_pdo->prepare($sql);
        $x = 1;
        if(count($queryParameters)){
            foreach($queryParameters as $param){
                if($param == null){
                   $this->_query->bindValue($x, PDO::PARAM_NULL); 
                }
                else{
                   $this->_query->bindValue($x, $param);
                }
                $x++;
            }
        } 
        $this->_query->execute();
        $this->_count = $this->_query->rowCount();
        return $this;
    }
    
    /*
     * For mupltiple insert query
     * Structure for parameter array :
     *  [
     *   [0] => ["value 1","Value 2","Value 3" ...], //Data set for iteration 1
     *   [1] => ["Value 1","Value 2","Value 3",...], //Data set for iteration 2
     *   ....
     *  ]   
     */
    
    public function batchInsert($sql = "",$paramSet = array(),$isTransaction = true){
        if($sql == ""){
            throw new CustomException("SQL query string not found");
        }
        else{
            if($isTransaction){
               $this->_pdo->beginTransaction();
            }
            $this->_query = $this->pdo->prepare($sql);
            if(count($paramSet)){
                $iterationCount = 1;
                foreach($paramSet as $params){
                   if(!count($params)){
                     throw new CustomException("No Parameter set found for iteration - $iterationCount.");  
                   }
                   else{
                       $x = 1;
                       foreach($params as $paramValue){
                          $this->_query->bindValue($x,$paramValue);
                          $x++;
                       }
                       $this->_query->execute();
                       $iterationCount++;
                   }
                }
                if($isTransaction){
                   $this->_pdo->commit();
                }
                return $this;
            }
            else{
                throw new CustomException("No parameter set found");
            }
        }
    }  
    
    public function insert($table = "",$insertParams = array()){
        if($table == ""){
            throw new CustomException("Table name not provided");
        }
        else if(!count($insertParams)){
            throw new CustomException("Values to be inserted not provided");
        }
        else{
            $insertParamCount = count($insertParams);
            $x = 1;
            $values = '';
            foreach($insertParams as $field => $fieldValue){
                 $values .= '?';
                 if($x < $insertParamCount){
                     $values .= ', '; 
                 }
                 $x++;
             }
            $insertParamName = array_keys($insertParams);
            $this->_sql = "INSERT INTO `$table` (`".implode('`, `',$insertParamName)."`) VALUES ($values)";
            $this->executeQuery($insertParams);
            return $this;
        }
    }
    
    public function update($table = "",$updateParams = array(),$whereParams = array()){
        if($table == ""){
            throw new CustomException("Table name not provided");
        }
        else if(!count($updateParams)){
            throw new CustomException("Values to be updated not provided");
        }
        else{
            $querySet = array();
            $updateParamCount = count($updateParams);
            $x = 1;
            foreach($updateParams as $updateParamName => $updateParamValue){
                 $values = '';
                 $values .= "`$updateParamName` = ?";
                 $querySet[] = $updateParamValue; 
                 if($x < $updateParamCount){
                     $values .= ', '; 
                 }
                 $x++;
             }
            $this->_sql = "UPDATE `$table` SET $values "; 
            if(count($whereParams)){
                 $whereParamCount = count($whereParams);
                 $x = 1;
                 foreach($whereParams as $whereParamName => $whereParamValue){
                   $values = '';
                   $values .= "`$whereParamName` = ? ";
                   $querySet[] = $whereParamValue; 
                   if($x < $whereParamCount){
                       $values .= 'AND '; 
                   }
                   $x++;
                 }
                 $this->_sql .= "WHERE $values";
            }
            $this->executeQuery($querySet);
            return $this;
        }
    }
    
    public function delete($table,$whereParams = array()){
           if($table == ""){
               throw new CustomException("Table name not provided");
           }
           else{
               $querySet = array();
               if(count($whereParams)){
                    $whereParamCount = count($whereParams);
                    $x = 1;
                    foreach($whereParams as $whereParamName => $whereParamValue){
                      $values = '';
                      $values .= "`$whereParamName` = ?";
                      $querySet[] = $whereParamValue; 
                      if($x < $whereParamCount){
                          $values .= ', '; 
                      }
                      $x++;
                    }
                    $this->_sql = "DELETE FROM `$table` WHERE $values";
               }
               $this->executeQuery($querySet);
               return $this;
           }
    }
    
    public function select($table = "",$whereParams = array(),$selectParams = array(),$extras = ""){
        if($table == ""){
            throw new CustomException("Table name not provided");
        }
        else{
            $selectSet = (count($selectParams)) ? "`".implode('`,`',$selectParams)."`" : '*';
            $values = "";
            if(count($whereParams)){
                 $querySet = array();
                 $whereParamCount = count($whereParams);
                 $x = 1;
                 foreach($whereParams as $whereParamName => $whereParamValue){
                   $values .= "`$whereParamName` = ? ";
                   $querySet[] = $whereParamValue; 
                   if($x < $whereParamCount){
                       $values .= 'AND '; 
                   }
                   $x++;
                 }
                 $this->_sql = "SELECT $selectSet FROM `$table` WHERE $values $extras";
            }
            $this->executeQuery($querySet);
            return $this;
        }
    }
    
    /**
     * Id of comp.sc. of btech,level 2, 
     * [
*        "value" => "Corresponding Value".
*        "nextLevel" => [
*                          "level-1 ColumnId ID-1" => [
*                                                       "value" => "Corresponding Value",
*                                                       "nextLevel" => null
*                                                     ]
*                          "level-1 ColumnId ID-2" => [
     *                                                  "value" => "Corresponding Value",
     *                                                  "nextLevel" => null
     *                                                ]
     *                  ]                                          
     *  ] 
     * 
     * 
     * 
     */
    
    public function traceDown($table  = "",$id = "",$level = 0,$indexIdName = "id",$parentIdName = "parent_id",$valueIdName = "name",$array = array()){
            if($table == ""){
                throw new CustomException("Table name not provided");
            }
            else if(!$level){
                throw new CustomException("Recursion level not specified");
            }
            else if(!is_int($level)){
                throw new CustomException("Level of recursion should be an integer");
            }
            else if($id == ""){
                throw new CustomException("Id for tracing not provided");
            }
            else{
                if($this->executeQuery(array($id),"SELECT * FROM `$table` WHERE `$parentIdName` = ?")->count()){
                    $results = $this->results();
                    foreach($results as $result){
                        $array[$result[$indexIdName]] = [
                          "value" =>  $result[$valueIdName],
                        ];
                        $array[$result[$indexIdName]]["Level Down"] = ($level - 1 > 0) ? $this->traceDown($table,$result[$indexIdName],$level-1,$indexIdName,$parentIdName) : -1;
                    }
                    return $array;
                }
                else{
                    return null;
                }  
            }
    }
    
    public function traceUp($table = "",$id = "",$level = 0,$indexIdName = "id",$parentIdName = "parent_id",$valueIdName = "name",$array = array()){
        if($table == ""){
            throw new CustomException("Table name not provided");
        }
        else if(!$level){
            throw new CustomException("Recursion level not specified");
        }
        else if(!is_int($level)){
            throw new CustomException("Level of recursion should be an integer");
        }
        else if($id == ""){
            throw new CustomException("Id for tracing not provided");
        }
        else{
            if($this->executeQuery(array($id),"SELECT * FROM `$table` WHERE `$indexIdName` = ?")->count()){
             $result = $this->results()[0];    
             $array[$result[$indexIdName]] = [
               "value" => $result[$valueIdName]  
             ];
             $array[$result[$indexIdName]]["Level Up"] = ($level - 1 > 0) ? $this->traceUp($table,$result[$parentIdName],$level-1,$indexIdName,$parentIdName,$valueIdName) : null;
            }
            return $array;
        } 
    }
    
    public function results(){
        $this->_results = $this->_query->fetchAll(PDO::FETCH_ASSOC);
        return $this->_results;
    }
    
    public function rowsAffected(){
        return $this->_count;
    }
    
    public function count(){
        return $this->_count;
    }
    
}
