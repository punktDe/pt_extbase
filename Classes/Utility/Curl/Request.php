<?php
namespace PunktDe\PtExtbase\Utility\Curl;

/*
 *  (c) 2018 punkt.de GmbH - Karlsruhe, Germany - https://punkt.de
 *  All rights reserved.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class Request
{
    /**
     * @var array
     */
    protected $curlOptions = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true
    ];


    /**
     * @var string
     */
    protected $url;


    /**
     * @var array
     */
    protected $header;


    /**
     * Post Data to a defined URL
     *
     * @param  string $data
     * @return Response
     */
    public function post($data = ''): Response
    {
        $request = $this->buildRequest();

        curl_setopt_array($request, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data
        ]);

        return $this->executeRequest($request);
    }

    /**
     * get data of a defined URL
     *
     * @return Response
     */
    public function get(): Response
    {
        $request = $this->buildRequest();

        return $this->executeRequest($request);
    }

    /**
     * delete data from a defined URL
     *
     * @return Response
     */
    public function delete(): Response
    {
        $request = $this->buildRequest();

        curl_setopt_array($request, [
            CURLOPT_CUSTOMREQUEST => 'DELETE'
        ]);

        return $this->executeRequest($request);
    }


    /**
     * @return resource
     */
    protected function buildRequest()
    {
        $request = curl_init($this->url);

        curl_setopt_array($request, $this->curlOptions);

        if (count($this->header)) {
            curl_setopt($request, CURLOPT_HTTPHEADER, $this->header);
        }

        return $request;
    }


    /**
     * @param $request
     * @return Response
     */
    protected function executeRequest($request): Response
    {
        $resultData = curl_exec($request);

        $response = GeneralUtility::makeInstance(Response::class, $request, $this, $resultData);

        return $response;
    }


    /**
     * @param string $url
     * @return Request
     */
    public function setUrl(string $url): Request
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $proxyUrl
     * @return Request
     */
    public function setProxy(string $proxyUrl): Request
    {
        $this->setCurlOption(CURLOPT_PROXY, $proxyUrl);
        return $this;
    }


    /**
     * @param $verifySSL
     * @return Request
     */
    public function setVerifySSL($verifySSL): Request
    {
        $this->setCurlOption(CURLOPT_SSL_VERIFYPEER, $verifySSL);
        return $this;
    }


    /**
     * @param integer $timeOut
     * @return Request
     */
    public function setTimeOut(int $timeOut): Request
    {
        $this->setCurlOption(CURLOPT_TIMEOUT, $timeOut);
        return $this;
    }


    /**
     * @param string $cookieFilePath
     * @return Request
     */
    public function useCookiesFromFile(string $cookieFilePath): Request
    {
        $this->setCurlOption(CURLOPT_COOKIEJAR, $cookieFilePath);
        $this->setCurlOption(CURLOPT_COOKIEFILE, $cookieFilePath);
        return $this;
    }


    /**
     * @param string $curlOptionKey
     * @param mixed $curlOptionValue
     * @return Request
     */
    public function setCurlOption(string $curlOptionKey, $curlOptionValue): Request
    {
        $this->curlOptions[$curlOptionKey] = $curlOptionValue;
        return $this;
    }


    /**
     * @param string $curlOptionKey
     * @return mixed
     */
    public function getCurlOptions(string $curlOptionKey)
    {
        if (isset($this->curlOptions[$curlOptionKey])) {
            return $this->curlOptions[$curlOptionKey];
        } else {
            return null;
        }
    }


    /**
     * @param string $key
     * @param string $value
     */
    public function addHeader(string $key, string $value)
    {
        $this->header[] = $key . ':' . $value;
    }
}
