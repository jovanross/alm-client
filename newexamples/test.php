<?php
/**
 * Created by PhpStorm.
 * User: jross0
 * Date: 8/7/17
 * Time: 4:53 PM
 */

require_once '../vendor/autoload.php';


$client = new AlmClient\AlmClient();

$client->Connect('https://alm.cscinfo.com','user','password');

$res = $client->GetDomains();

