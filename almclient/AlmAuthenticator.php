<?php

namespace AlmClient;

class AlmAuthenticator
{

    private static $Instance;

    protected $user;

    protected $password;

    private function GetUser()
    {
        return $this->user;
    }

    private function SetUser($user)
    {
        $this->user = $user;
    }

    private function GetPassword()
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

    public function Authenticate($host, $user, $password)
    {

        \AlmClient\AlmRoutes::GetInstance($host);

        $this->SetUser($user);

        $this->SetPassword($password);

    }

    public function Login()
    {
        try {
            $headers = array("GET /HTTP/1.1", "Authorization: Basic " . base64_encode($this->GetUser() . ":" . $this->GetPassword()));

            $isValid = \AlmClient\AlmCurl::GetInstance()->SetHeaders($headers)->Execute(\AlmClient\AlmRoutes::GetInstance()->GetLoginUrl())->ValidResponse();

            if (!$isValid) {

                \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();

            } else {

                throw new \Exception('Authentication error : Invalid response returned');

            }

            return $this;

        } catch (\Exception $e) {

            \AlmClient\AlmCurlCookieJar::GetInstance()->RemoveCurlCookieJar();
            throw new \Exception('Authentication error : ' . $e->getMessage());

        }

    }

    public function IsAuthenticated()
    {
        try {

            \AlmClient\AlmCurl::GetInstance()->Execute(\AlmClient\AlmRoutes::GetInstance()->GetAuthenticationCheckUrl());

            if (\AlmClient\AlmCurl::GetInstance()->ValidResponse()) {
                return true;
            }

            return false;

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
