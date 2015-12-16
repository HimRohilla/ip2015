<?php

/*
 * Trace down function test
 */

$db = DB::getInstance();

$results = $db->traceDown("department_course_subject",2,2);

echo "<pre>",print_r($results),"</pre>";

/*
 * Trace up function test
 */

echo "<br><br>";

$results = $db->traceup("department_course_subject",6,2);

echo "<pre>",print_r($results),"</pre>";

