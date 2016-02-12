<?php
/**
 * Created by PhpStorm.
 * User: Stepan
 * Date: 10.02.2016
 * Time: 11:44
 */

namespace StepanSib\AlmClient;

use StepanSib\AlmClient\Exception\AlmCurlException;

Class AlmCurl
{

    const HTTP_401 = '401: unauthenticated request';
    const HTTP_403 = '403: unauthenticated request';
    const HTTP_404 = '404: resource not found';
    const HTTP_405 = '405: method not supported by resource';
    const HTTP_406 = '406: unsupported ACCEPT type';
    const HTTP_415 = '415: unsupported request content type';
    const HTTP_500 = '500: Internal server error';

    /** @var resource */
    protected $curl;

    /** @var  mixed */
    protected $result;

    /** @var  mixed */
    protected $info;

    /** @var AlmCurlCookieStorage */
    protected $cookieStorage;

    /**
     * AlmCurl constructor.
     * @param AlmCurlCookieStorage $cookieStorage
     */
    public function __construct(AlmCurlCookieStorage $cookieStorage)
    {
        $this->cookieStorage = $cookieStorage;
        return $this;
    }

    /**
     * @return $this
     */
    private function curlInit()
    {
        if (null === $this->curl) {
            $this->curl = curl_init();
            curl_setopt($this->curl, CURLOPT_HEADER, 0);
            curl_setopt($this->curl, CURLOPT_HTTPGET, 1);
            curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 5); //connection timeout
            curl_setopt($this->curl, CURLOPT_TIMEOUT, 30); //overall timeout

            $this->clearResults();
        }

        return $this;
    }

    public function setHeaders(array $headers = array())
    {
        $this->curlInit();

        if (count($headers) > 0) {
            curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
            return $this;
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array());
        return $this;
    }

    /**
     * @param $url
     * @return $this
     * @throws AlmCurlException
     */
    public function exec($url)
    {
        $this->curlInit();

        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->cookieStorage->getCurlCookieFile());

        $result = curl_exec($this->curl);

        if (curl_errno($this->curl) === 0) {
            $this->result = $result;
            $this->info = curl_getinfo($this->curl);

            if (!$this->isResponseValid()) {
                $httpCodeConstantName = get_class($this) . '::HTTP_' . $this->getHttpCode();
                if (defined($httpCodeConstantName)) {
                    throw new AlmCurlException(constant($httpCodeConstantName));
                } else {
                    throw new AlmCurlException('Disallowed HTTP response code: ' . $this->getHttpCode());
                }
            }
        } else {
            throw new AlmCurlException('Curl error: ' . curl_error($this->curl));
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return string|null
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return string|null
     */
    public function getHttpCode()
    {
        if (null !== $this->getInfo()) {
            $info = $this->getInfo();
            return $info['http_code'];
        } else {
            return null;
        }
    }

    /**
     * @return $this
     * @throws AlmCurlException
     */
    public function createCookie()
    {
        $this->curlInit();

        if ($this->curl !== null) {
            curl_setopt($this->curl, CURLOPT_COOKIEJAR, $this->cookieStorage->createCurlCookieFile()->getCurlCookieFile());
        } else {
            throw new AlmCurlException('Curl not initialized');
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function clearResults()
    {
        $this->result = null;
        $this->info = null;

        return $this;
    }

    /**
     * @return $this
     */
    public function close()
    {
        if ($this->curl !== null) {
            curl_close($this->curl);
            $this->curl = null;

            $this->clearResults();
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isResponseValid()
    {
        if ($this->getHttpCode() == '200' || $this->getHttpCode() == '201') {
            return true;
        }
        return false;
    }
}
