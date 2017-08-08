<?php
/**
 * Created by PhpStorm.
 * User: jross0
 * Date: 8/8/17
 * Time: 3:29 PM
 */

namespace AlmClient;

class AlmTestAssets
{

    private static $Instance;

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        return self::$Instance;
    }

    protected function VerifyState()
    {
        $this->VerifyDomain();
        $this->VerifyProject();
    }

    protected function VerifyDomain()
    {
        if(!\AlmClient\AlmRoutes::GetInstance()->GetDomain()){

            throw new \Exception('VerifyDomain error : You must specify a domain prior to this operation');

        }
    }

    protected function VerifyProject()
    {
        if(!\AlmClient\AlmRoutes::GetInstance()->GetProject()){

            throw new \Exception('VerifyProject error : You must specify a project prior to this operation');

        }
    }

    public function __construct(){}

    public function GetDomains($domain = null)
    {
        try {

            $isValid = \AlmClient\AlmCurl::GetInstance()
                ->SetGetHeaders()
                ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetDomainsUrl($domain))
                ->ValidResponse();

            if (!$isValid) {

                \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();

                throw new \Exception('GetDomains error : Invalid response returned');

            }

            return \AlmClient\ALMXMLMessage::GetInstance()->DeconstructMessage(\AlmClient\AlmCurl::GetInstance()->GetResult());

        } catch (\Exception $e) {

            \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();
            throw new \Exception('GetDomains error : ' . $e->getMessage());

        }
    }

    public function GetProjects($project = null)
    {
        try {

            $this->VerifyDomain();

            $isValid = \AlmClient\AlmCurl::GetInstance()
                ->SetGetHeaders()
                ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetDomainProjectsUrl($project))
                ->ValidResponse();

            if (!$isValid) {

                \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();

                throw new \Exception('GetProjects error : Invalid response returned');

            }

            return \AlmClient\ALMXMLMessage::GetInstance()->DeconstructMessage(\AlmClient\AlmCurl::GetInstance()->GetResult());

        } catch (\Exception $e) {

            \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();
            throw new \Exception('GetProjects error : ' . $e->getMessage());

        }
    }

    public function GetTestPlanFolders($id = null, $parentId = null)
    {
        try {

            $this->VerifyState();

            $isValid = \AlmClient\AlmCurl::GetInstance()
                ->SetGetHeaders()
                ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetTestPlanFolders($id, $parentId))
                ->ValidResponse();

            if (!$isValid) {

                \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();

                throw new \Exception('GetTestPlanFolders error : Invalid response returned');

            }

            return \AlmClient\ALMXMLMessage::GetInstance()->DeconstructMessage(\AlmClient\AlmCurl::GetInstance()->GetResult());

        } catch (\Exception $e) {

            \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();
            throw new \Exception('GetTestPlanFolders error : ' . $e->getMessage());

        }
    }

}