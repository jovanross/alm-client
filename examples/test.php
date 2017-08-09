<?php
/**
 * Created by PhpStorm.
 * User: jross0
 * Date: 8/7/17
 * Time: 4:53 PM
 */

require_once '../vendor/autoload.php';

$client = new AlmClient\AlmClient();

$client->Connect('https://alm-server-name','username','password');

$res = $client->GetDomains();

$client->SetDomain('ETP');

$res = $client->GetProjects();

$client->SetProject('ETP');

$res = $client->GetTestPlanFolders(null, 2);

//\AlmClient\AlmClient::GetInstance()->PersistSession();

echo 'Done';