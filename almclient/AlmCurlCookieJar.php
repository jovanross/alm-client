<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 10.02.2016
 * Time: 12:32
 */

namespace AlmClient;

class AlmCurlCookieJar
{

    private static $Instance;

    const MEMORY_LIMIT = 5242880;

    private $cookieJar;

    public function GetCookieJar()
    {

        try {

            if (!$this->CurlCookieJarExists()) {

                $this->SetCookieJar(fopen('php://temp/maxmemory:'.self::MEMORY_LIMIT, 'r+'));

            }

            return $this->cookieJar;

        } catch(\Exception $e){

            throw new \Exception($e->getMessage());

        }

    }

    public function __construct(){}

    private function SetCookieJar($cookieJar)
    {
        $this->cookieJar = $cookieJar;
    }

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();

            self::$Instance->GetCookieJar();
        }

        return self::$Instance;
    }

    public function RemoveCurlCookieJar()
    {

        try {

            if ($this->CurlCookieJarExists()) {

                fclose($this->GetCookieJar());

            }

            return $this;

        } catch(\Exception $e){

            throw new \Exception($e->getMessage());

        }
    }

    public function CurlCookieJarExists()
    {

        try {

            if ($this->GetCookieJar()) {

                if (file_exists($this->GetCookieJar())) {

                    return true;

                }

            }

            return false;

        } catch(\Exception $e){

            throw new \Exception($e->getMessage());

        }

    }

}
