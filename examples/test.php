<?php
/**
 * Created by PhpStorm.
 * User: jross0
 * Date: 8/7/17
 * Time: 4:53 PM
 */

require_once '../vendor/autoload.php';

$client = new AlmClient\AlmClient();

$client->Connect('alm.cscinfo.com','','');

$res = $client->GetDomains();

$client->SetDomain('SANDBOX');

$res = $client->GetProjects();

$client->SetProject('Testing');

$res = $client->GetTestPlanFolders(null, 0);

$res = $client->CreateTest();

//\AlmClient\AlmClient::GetInstance()->PersistSession();

print_r($res);