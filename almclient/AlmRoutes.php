<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 10.02.2016
 * Time: 12:58
 */

namespace AlmClient;

class AlmRoutes
{

    private static $Instance;

    protected $host;

    protected $domain;

    protected $project;

    public static function GetInstance($host = null)
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        if($host) {

            self::$Instance->SetHost($host);

        }

        return self::$Instance;
    }

    public function __construct($host = null){

        if($host) {

            $this->SetHost($host);

        }

    }

    public function SetDomain($domain)
    {
        $this->domain = $domain;
    }

    public function GetDomain()
    {
        return $this->domain;
    }

    public function SetProject($project)
    {
        $this->project = $project;
    }

    public function GetProject()
    {
        return $this->project;
    }

    private function GetHost()
    {
        if(empty($this->host)){

            throw new \Exception('Host has not been set');

        }
        return $this->host;
    }

    private function SetHost($host)
    {
        $this->host = $host;
    }

    public function Init($host)
    {
        $this->SetHost($host);
    }

    public function GetLoginUrl()
    {
        return $this->GetHost() . '/qcbin/authentication-point/authenticate';
    }

    public function GetLogoutUrl()
    {
        return $this->GetHost() . '/qcbin/authentication-point/logout';
    }

    public function GetAuthenticationCheckUrl()
    {
        return $this->GetHost() . '/qcbin/rest/is-authenticated';
    }

    public function GetSiteSessionUrl()
    {
        return $this->GetHost() . '/qcbin/rest/site-session';
    }

    public function GetDomainsUrl($domain = null, $includeProjects = false)
    {
        if($domain !== null) $domain = '/'.$domain;
        if($includeProjects) $domain .= '?include-projects-info=y';

        return $this->GetHost() . '/qcbin/rest/domains' . $domain;
    }

    public function GetDomainProjectsUrl($project = null)
    {
        if($project !== null) $project = '/'.$project;
        return $this->GetHost() . '/qcbin/rest/domains/' . $this->GetDomain() . '/projects' . $project;
    }

    public function GetTestPlanFolders($id = null, $parentId = null)
    {
        if($id !== null) $id = '/'.$id;
        if($parentId !== null) $parentId = '?query={parent-id['.$parentId.']}';

        return $this->GetHost() . '/qcbin/rest/domains/' . $this->GetDomain() . '/projects/' . $this->GetProject(). '/test-folders' . $id . $parentId;
    }

    public function GetEntityUrl($entityType, $entityId = null)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->GetDomain() . '/projects/' . $this->GetProject() . '/' . $entityType;
        if (null !== $entityId) {
            $url .= '/' . $entityId;
        }
        return $url;
    }

    public function GetEntityFieldsUrl($entityType, $onlyRequiredFields = false)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->GetDomain() . '/projects/' . $this->GetProject() . '/customization/entities/' . $entityType . '/fields';
        if ($onlyRequiredFields) {
            $url .= '?required=true';
        }
        return $url;
    }

    public function GetListsUrl($listId = null)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->GetDomain() . '/projects/' . $this->GetProject() . '/customization/lists';
        if (null !== $listId) {
            $url .= '?id=' . $listId;
        }
        return $url;
    }

    public function GetEntityCheckoutUrl($entityType, $entityId)
    {
        return $this->GetHost() . '/qcbin/rest/domains/' . $this->GetDomain() . '/projects/' . $this->GetProject() . '/' . $entityType . '/' . $entityId . '/versions/check-out';
    }

    public function GetEntityCheckinUrl($entityType, $entityId)
    {
        return $this->GetHost() . '/qcbin/rest/domains/' . $this->GetDomain() . '/projects/' . $this->GetProject() . '/' . $entityType . '/' . $entityId . '/versions/check-in';
    }

    public function GetEntityLockUrl($entityType, $entityId)
    {
        return $this->GetHost() . '/qcbin/rest/domains/' . $this->GetDomain() . '/projects/' . $this->GetProject() . '/' . $entityType . '/' . $entityId . '/lock';
    }

}
