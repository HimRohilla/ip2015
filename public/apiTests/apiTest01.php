<?php
require_once __DIR__."/../../includes/Faker-master/src/autoload.php";

use Faker\Factory;

$genderArray = array("Male","Female","Transgender");

$db = DB::getInstance();

$faker = Factory::create();
//
//$userEntity = array();
//    
$adminId = $faker->uuid;
//
//$userEntity["UID"] = $uuid;
//
//$name = $faker->name;
//
//$userEntity["name"] = $name;
//
//$email = $faker->email;
//
//$userEntity["email"] = $email;
//
//$dob = $faker->date();
//
//$userEntity["password"] = HASH::generatePassword($email);
//
//$userEntity["DOB"] = $dob;
//
//$gender = $genderArray[array_rand($genderArray)];
//
//$userEntity["gender"] = $gender;
//
//$userEntity["category_id"] = mt_rand(1, 5);
//
//$userEntity["contact"] = json_encode(array("$faker->phoneNumber","$faker->phoneNumber","$faker->phoneNumber"),true);
//
//$userEntity["address"] = json_encode(array("Correspondence Address" => $faker->address,"Permanent Address" => $faker->address),true);
//
//$userEntity["modified_by"] = $uuid;
//
//echo "<pre>",print_r($userEntity),"</pre>";
//
//echo "<br><br>";
//
//$db->insert("user",$userEntity);
//
//$userTypeEntity = array("user_id" => $uuid, "usertype_id" => 9,"modified_by" => $uuid);
//
//$db->insert("user_usertype",$userTypeEntity);

for($i = 0 ; $i < 20 ; $i++){
    $faker = Factory::create();

    $userEntity = array();
    
    $uuid = $faker->uuid;
    
    $userEntity["UID"] = $uuid;
    
    $name = $faker->name;
    
    $userEntity["name"] = $name;
    
    $email = $faker->email;
    
    $userEntity["email"] = $email;
    
    $dob = $faker->date();
    
    $userEntity["password"] = HASH::generatePassword($email);
    
    $userEntity["DOB"] = $dob;
    
    $gender = $genderArray[array_rand($genderArray)];
    
    $userEntity["gender"] = $gender;
    
    $userEntity["category_id"] = mt_rand(1, 5);
    
    $userEntity["contact"] = json_encode(array("$faker->phoneNumber","$faker->phoneNumber","$faker->phoneNumber"),true);
    
    $userEntity["address"] = json_encode(array("Correspondence Address" => $faker->address,"Permanent Address" => $faker->address),true);
    
    $userEntity["modified_by"] = $adminId;
    
    $db->insert("user",$userEntity);
    
    $userTypeEntity = array("user_id" => $uuid, "usertype_id" => mt_rand(1,4),"modified_by" => $adminId);
    
    $db->insert("user_usertype",$userTypeEntity);
}



