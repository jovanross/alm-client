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

    protected $appended = /*'?login-form-required=y'*/null;

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
        return $this->GetHost() . '/qcbin/authentication-point/authenticate' . $this->appended;
    }

    public function GetLogoutUrl()
    {
        return $this->GetHost() . '/qcbin/authentication-point/logout' . $this->appended;
    }

    public function GetAuthenticationCheckUrl()
    {
        return $this->GetHost() . '/qcbin/rest/is-authenticated' . $this->appended;
    }

    public function GetDomainsUrl($domain = null)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $domain . $this->appended;
        return $url;
    }

    public function GetProjectUrl($project = null)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $project . $this->appended;
        return $url;
    }

    public function GetEntityUrl($entityType, $entityId = null)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/' . $entityType . $this->appended;
        if (null !== $entityId) {
            $url .= '/' . $entityId;
        }
        return $url;
    }

    public function GetEntityFieldsUrl($entityType, $onlyRequiredFields = true)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/customization/entities/' . $entityType . '/fields' . $this->appended;
        if ($onlyRequiredFields) {
            $url .= '?required=true';
        }
        return $url;
    }

    public function GetListsUrl($listId = null)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/customization/lists' . $this->appended;
        if (null !== $listId) {
            $url .= '?id=' . $listId;
        }
        return $url;
    }

    public function GetEntityCheckoutUrl($entityType, $entityId)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/' . $entityType . '/' . $entityId . '/versions/check-out' . $this->appended;
        return $url;
    }

    public function GetEntityCheckinUrl($entityType, $entityId)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/' . $entityType . '/' . $entityId . '/versions/check-in' . $this->appended;
        return $url;
    }

    public function GetEntityLockUrl($entityType, $entityId)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/' . $entityType . '/' . $entityId . '/lock' . $this->appended;
        return $url;
    }

}
