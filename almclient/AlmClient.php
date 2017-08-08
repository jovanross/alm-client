<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 10.02.2016
 * Time: 12:43
 */

namespace AlmClient;

Class AlmClient
{

    private static $Instance;

    public function SetDomain($domain)
    {
        \AlmClient\AlmRoutes::GetInstance()->SetDomain($domain);
    }

    public function GetDomain()
    {
        return \AlmClient\AlmRoutes::GetInstance()->GetDomain();
    }

    public function SetProject($project)
    {
        \AlmClient\AlmRoutes::GetInstance()->SetProject($project);
    }

    public function GetProject()
    {
        return \AlmClient\AlmRoutes::GetInstance()->GetProject();
    }

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        return self::$Instance;
    }

    public function __construct(){}

    public function Connect($host, $username, $password, $domain = null, $project = null)
    {
        \AlmClient\AlmAuthenticator::GetInstance()->Authenticate($host, $username, $password, $domain, $project);

    }

    public function GetDomains($domain = null)
    {
        return \AlmClient\AlmTestAssets::GetInstance()->GetDomains($domain);

    }

    public function GetProjects($project = null)
    {
        return \AlmClient\AlmTestAssets::GetInstance()->GetProjects($project);

    }

    public function GetTestPlanFolders($id = null, $parentId = null)
    {
        return \AlmClient\AlmTestAssets::GetInstance()->GetTestPlanFolders($id, $parentId);

    }

    public function Disconnect()
    {
        \AlmClient\AlmAuthenticator::GetInstance()->Logout();

    }

    public function PersistSession()
    {
        \AlmClient\AlmCurlCookieJar::GetInstance()->StoreSession();

    }

}
