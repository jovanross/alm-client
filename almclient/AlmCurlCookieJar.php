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

                $this->SetCookieJar(tempnam('/tmp','AlmClient_'));

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

                @unlink($this->GetCookieJar());

                $this->SetPointer(null);
                $this->SetCookieJar(null);

            }

            return $this;

        } catch(\Exception $e){

            throw new \Exception($e->getMessage());

        }
    }

    protected function EncryptSession($data)
    {
        try{

            if (! defined('CRYPT_KEY')) {
                define('CRYPT_KEY', md5('temporaryencryptionkey'));
            }

            return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, CRYPT_KEY, $data, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));

        } catch (\Exception $e) {

            throw new \Exception('EncryptSession error : ' . $e->getMessage());

        }
    }

    protected function DecryptSession($data)
    {
        try{

            if (! defined('CRYPT_KEY')) {
                define('CRYPT_KEY', md5('temporaryencryptionkey'));
            }

            return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, CRYPT_KEY, base64_decode($data), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));

        } catch (\Exception $e) {

            throw new \Exception('EncryptSession error : ' . $e->getMessage());

        }
    }

    public function GetSession()
    {
        try {

            @session_start(['name' => 'AlmClient','cookie_lifetime' => 86400]);

            if(isset($_SESSION) && isset($_SESSION['AlmClient'])) {

                $data = @unserialize($this->DecryptSession($_SESSION['AlmClient']));

                file_put_contents($this->GetCookieJar(), $data);

            }

            return $this;

        } catch(\Exception $e){

            throw new \Exception($e->getMessage());

        }
    }

    public function StoreSession()
    {
        try {

            @session_start(['name' => 'AlmClient','cookie_lifetime' => 86400]);

            $data = $this->EncryptSession(@serialize(file_get_contents($this->GetCookieJar())));

            $_SESSION['AlmClient'] = $data;

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
            self::$Instance->GetSession();

        }

        return self::$Instance;
    }

}
