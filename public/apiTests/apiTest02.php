<?php
/*
 * select function test
 */

$db = DB::getInstance();

$results = $db->select("user",array("gender" => "Male"),"ORDER BY id")->results();

echo "<pre>",print_r($results),"</pre>";


/*
 * 
 */

$results = $db->select("user",array("id" => 1))->results();

echo "<pre>",print_r($results),"</pre>";