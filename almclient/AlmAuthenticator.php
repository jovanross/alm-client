<?php

namespace AlmClient;

class AlmAuthenticator
{

    private static $Instance;

    protected $user;

    protected $password;

    public function GetUser()
    {
        return $this->user;
    }

    private function SetUser($user)
    {
        $this->user = $user;
    }

    public function GetPassword()
    {
        return $this->password;
    }

    private function SetPassword($password)
    {
        $this->password = $password;
    }

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        return self::$Instance;
    }

    public function Authenticate($host, $user, $password, $domain = null, $project = null)
    {

        try {

            \AlmClient\AlmRoutes::GetInstance($host);

            $this->SetUser($user);

            $this->SetPassword($password);

            if($domain) \AlmClient\AlmRoutes::GetInstance()->SetDomain($domain);

            if($project) \AlmClient\AlmRoutes::GetInstance()->SetProject($project);

            $this->Login();

            $this->CreateSession();

        } catch (\Exception $e) {

            \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();
            throw new \Exception('Authentication error : ' . $e->getMessage());

        }

    }

    public function Login()
    {
        try {

            $isValid = \AlmClient\AlmCurl::GetInstance()
                ->SetAcceptHeader()
                ->BasicAuthHeader($this->GetUser(),$this->GetPassword())
                ->SetPost(\AlmClient\ALMXMLMessage::GetInstance()->ConstructMessage('alm-authentication',array('user' => $this->GetUser(), 'password' => $this->GetPassword())))
                ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetLoginUrl())
                ->ValidResponse();

            if (!$isValid) {

                \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();

                throw new \Exception('Login error : Invalid response returned');

            } else {

                //verify authentication
                $isValid = $this->IsAuthenticated();

                if (!$isValid) {

                    throw new \Exception('Login error : IsAuthenticated returned false');

                }

            }

        } catch (\Exception $e) {

            \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();
            throw new \Exception('Login error : ' . $e->getMessage());

        }

    }

    public function CreateSession()
    {
        try {

            $isValid = \AlmClient\AlmCurl::GetInstance()
                ->SetAcceptHeader()
                ->SetPost(\AlmClient\ALMXMLMessage::GetInstance()->ConstructMessage('session-parameters',array('client-type' => 'REST Client')))
                ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetSiteSessionUrl())
                ->ValidResponse();

            if (!$isValid) {

                \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();

                throw new \Exception('Create Session error : Invalid response returned');

            }

        } catch (\Exception $e) {

            \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();
            throw new \Exception('Login error : ' . $e->getMessage());

        }

    }

    public function IsAuthenticated()
    {
        try {

            //verify 'isAuthenticated'
            $isValid = \AlmClient\AlmCurl::GetInstance()
                ->SetGetHeaders()
                ->Execute(\AlmClient\AlmRoutes::GetInstance()->GetAuthenticationCheckUrl())
                ->ValidResponse();

            if (!$isValid) {

                return false;

            } else return true;

        } catch (\Exception $e) {

            \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();
            throw new \Exception('isAuthenticated error : ' . $e->getMessage());

        }
    }

    public function Logout()
    {
        try {

            \AlmClient\AlmCurl::GetInstance()->Execute(\AlmClient\AlmRoutes::GetInstance()->GetLogoutUrl());
            \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();

            return $this;

        } catch (\Exception $e) {

            throw new \Exception('Logout error : ' . $e->getMessage());
        }

    }

}
