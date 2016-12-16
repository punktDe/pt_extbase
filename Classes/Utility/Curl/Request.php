<?php
namespace PunktDe\PtExtbase\Utility\Curl;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lienert <lienert@punkt.de>
 *
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

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
    public function post($data = '')
    {
        $request = $this->buildRequest();

        curl_setopt_array($request, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data
        ]);

        return $this->executeRequest($request);
    }

    /**
     *  get data of a defined URL
     *
     * @return Response
     */
    public function get()
    {
        $request = $this->buildRequest();

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
    protected function executeRequest($request)
    {
        $resultData = curl_exec($request);

        $response =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('PunktDe\\PtExtbase\\Utility\\Curl\\Response', $request, $this, $resultData);

        return $response;
    }



    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $proxyUrl
     * @return $this
     */
    public function setProxy($proxyUrl)
    {
        $this->setCurlOption(CURLOPT_PROXY, $proxyUrl);
        return $this;
    }


    /**
     * @param $verifySSL
     * @return $this
     */
    public function setVerifySSL($verifySSL)
    {
        $this->setCurlOption(CURLOPT_SSL_VERIFYPEER, $verifySSL);
        return $this;
    }


    /**
     * @param integer $timeOut
     * @return $this
     */
    public function setTimeOut($timeOut)
    {
        $this->setCurlOption(CURLOPT_TIMEOUT, $timeOut);
        return $this;
    }


    /**
     * @param $cookieFilePath
     * @return $this
     */
    public function useCookiesFromFile($cookieFilePath)
    {
        $this->setCurlOption(CURLOPT_COOKIEJAR, $cookieFilePath);
        $this->setCurlOption(CURLOPT_COOKIEFILE, $cookieFilePath);
        return $this;
    }


    /**
     * @param $curlOptionKey
     * @param $curlOptionValue
     * @return $this
     */
    public function setCurlOption($curlOptionKey, $curlOptionValue)
    {
        $this->curlOptions[$curlOptionKey] = $curlOptionValue;
        return $this;
    }


    /**
     * @param string $curlOptionKey
     * @return mixed
     */
    public function getCurlOptions($curlOptionKey)
    {
        if (isset($this->curlOptions[$curlOptionKey])) {
            return $this->curlOptions[$curlOptionKey];
        } else {
            return null;
        }
    }


    /**
     * @param $key
     * @param $value
     */
    public function addHeader($key, $value)
    {
        $this->header[] = $key .':'. $value;
    }
}
