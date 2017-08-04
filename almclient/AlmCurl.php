<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 10.02.2016
 * Time: 11:44
 */

namespace AlmClient;

Class AlmCurl
{

    private static $Instance;

    protected $curl;

    protected $result;

    protected $info;

    public static function GetInstance()
    {
        if (is_null(self::$Instance)) {
            self::$Instance = new self();
        }

        self::$Instance->Init();

        return self::$Instance;
    }

    public function __construct(){

        \AlmClient\AlmCurlCookieJar::GetInstance();

    }

    private function Init()
    {
        if (null === $this->curl) {
            $this->curl = curl_init();
            curl_setopt($this->curl, CURLOPT_HEADER, 0);
            curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 10); //connection timeout
            curl_setopt($this->curl, CURLOPT_TIMEOUT, 30); //overall timeout

            $this->Reset();
        }

        return $this;
    }

    public function SetHeaders(array $headers = array())
    {
        $this->Init();

        if (count($headers) > 0) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        }

        return $this;
    }

    public function SetPost($body = null)
    {
        $this->Init();

        curl_setopt($this->curl, CURLOPT_POST, 1);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);

        return $this;
    }

    public function SetPut($body = null)
    {
        $this->Init();

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);

        return $this;
    }

    public function SetDelete($body = null)
    {
        $this->Init();

        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);

        return $this;
    }

    public function Execute($url)
    {

        $this->Init();

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, \AlmClient\AlmCurlCookieJar::GetInstance()->GetCookieJar());

        $result = curl_exec($this->curl);

        if (curl_errno($this->curl) === 0) {
            $this->result = $result;
            $this->info = curl_getinfo($this->curl);

            if (!$this->ValidResponse()) {

                if ($this->GetHttpCode() == '500') {
                    $error = $this->getInternalError();
                    throw new \Exception($error);
                }

                $httpCodeConstantName = get_class($this) . '::HTTP_' . $this->GetHttpCode();
                if (defined($httpCodeConstantName)) {
                    throw new \Exception(constant($httpCodeConstantName));
                }

                throw new \Exception('Disallowed HTTP response code: ' . $this->GetHttpCode());

            }
        } else {
            throw new \Exception('Curl error: ' . curl_error($this->curl));
        }

        $this->Close();
        return $this;
    }

    protected function GetInternalError()
    {
        $xml = simplexml_load_string($this->GetResult());
        if (false === $xml || !property_exists($xml, 'Title')) {

            return "Undefined error";

        }
        return $xml->Title[0];
    }

    public function GetResult()
    {
        return $this->result;
    }

    public function GetInfo()
    {
        return $this->info;
    }

    public function GetHttpCode()
    {
        if (null !== $this->GetInfo()) {

            $info = $this->GetInfo();
            return $info['http_code'];

        } else {

            return null;

        }
    }

    public function CreateCookie()
    {
        $this->Init();

        if ($this->curl !== null) {

            curl_setopt($this->curl, CURLOPT_COOKIEJAR, \AlmClient\AlmCurlCookieJar::GetInstance()->GetCookieJar());

        } else {

            throw new \Exception('Curl not initialized');

        }

        return $this;
    }

    protected function Reset()
    {
        $this->result = null;
        $this->info = null;

        return $this;
    }

    protected function Close()
    {
        if ($this->curl !== null) {

            curl_close($this->curl);
            $this->curl = null;

        }

        return $this;
    }

    public function ValidResponse()
    {
        if ($this->GetHttpCode() == '200' || $this->GetHttpCode() == '201') {

            return true;

        }

        return false;
    }
}
