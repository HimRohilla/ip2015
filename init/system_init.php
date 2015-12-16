<?php

define('DEBUG_MODE',true);

define('CUSTOM_ERROR_MESSAGE','');

/*
 * Global Configurations
 */
$GLOBALS['CONFIG'] = array(
    'DB' => array(
        'hostname' => 'localhost',
        'dbname' => 'ip2015_db',
        'username' => 'root',
        'password' => ''
    ),
    'PASSWORD' => array(
        'cost' => 10
    ),
    'URL' => "http://localhost/ip2015/"
);

require_once __DIR__.'/../classes/autoload.php';

SESSION::init_session();

if(!SESSION::exists("sitemap")){
    $sitemapJSONString = file_get_contents(__DIR__."/sitemap.json");
    SESSION::setSession("sitemap", json_decode($sitemapJSONString));
}



