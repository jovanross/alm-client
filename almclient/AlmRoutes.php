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

    public function GetEntityUrl($entityType, $entityId = null)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/' . $entityType;
        if (null !== $entityId) {
            $url .= '/' . $entityId;
        }
        return $url;
    }

    public function GetEntityFieldsUrl($entityType, $onlyRequiredFields = true)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/customization/entities/' . $entityType . '/fields';
        if ($onlyRequiredFields) {
            $url .= '?required=true';
        }
        return $url;
    }

    public function GetListsUrl($listId = null)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/customization/lists';
        if (null !== $listId) {
            $url .= '?id=' . $listId;
        }
        return $url;
    }

    public function GetEntityCheckoutUrl($entityType, $entityId)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/' . $entityType . '/' . $entityId . '/versions/check-out';
        return $url;
    }

    public function GetEntityCheckinUrl($entityType, $entityId)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/' . $entityType . '/' . $entityId . '/versions/check-in';
        return $url;
    }

    public function GetEntityLockUrl($entityType, $entityId)
    {
        $url = $this->GetHost() . '/qcbin/rest/domains/' . $this->domain . '/projects/' . $this->project . '/' . $entityType . '/' . $entityId . '/lock';
        return $url;
    }

}
