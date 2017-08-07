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

    private $cookieJar = null;

    private $pointer = null;

    private function GetPointer()
    {
        return $this->pointer;
    }

    private function SetPointer($pointer)
    {
        $this->pointer = $pointer;
    }

    private function SetCookieJar($cookieJar)
    {

        try {

            if(!empty($cookieJar)){

                $this->SetPointer(fopen($cookieJar, 'r+'));

            }

            $this->cookieJar = $cookieJar;

        } catch(\Exception $e){

            throw new \Exception($e->getMessage());

        }

    }

    public function GetCookieJar()
    {

        try {

            if (empty($this->GetPointer())) {

                $this->SetCookieJar(tempnam('/tmp','AlmClient'));

            }

            return $this->cookieJar;

        } catch(\Exception $e){

            throw new \Exception($e->getMessage());

        }

    }

    public function RemoveCurlCookieJar()
    {

        try {

            if($this->GetPointer()){

                @fclose($this->GetPointer());

                $this->SetPointer(null);
                $this->SetCookieJar(null);

            }

            return $this;

        } catch(\Exception $e){

            throw new \Exception($e->getMessage());

        }
    }

    public function __construct(){}

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();

            self::$Instance->GetCookieJar();
        }

        return self::$Instance;
    }

}
