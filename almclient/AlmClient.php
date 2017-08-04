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

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        return self::$Instance;
    }

    public function __construct(){}

    public function Connect($host, $username, $password)
    {
        \AlmClient\AlmAuthenticator::GetInstance()->Authenticate($host, $username, $password);

    }

}
