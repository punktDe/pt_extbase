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

class Request {

	/**
	 * @var string
	 */
	protected $url;


	/**
	 * @var string
	 */
	protected $proxyUrl = '';


	/**
	 * @var \Tx_PtExtbase_Logger_Logger
	 */
	protected $logger;


	/**
	 * @var integer
	 */
	protected $timeOut;


	/**
	 * @var string
	 */
	protected $header;


	/**
	 * @var integer
	 */
	protected $httpCode;


	/**
	 * @var string
	 */
	protected $result;


	/**
	 * @var boolean
	 */
	protected $requestSucceeded = FALSE;


	/**
	 * @var string
	 */
	protected $cookieFilePath;


	/**
	 * @var integer
	 */
	protected $errorNumber;


	/**
	 * @var string
	 */
	protected $errorMessage;


	/**
	 * @param \Tx_PtExtbase_Logger_Logger $logger
	 * @return void
	 */
	public function inject(\Tx_PtExtbase_Logger_Logger $logger) {
		$this->logger = $logger;
	}


	/**
	 * Post Data to a defined URL
	 *
	 * @param string $data
	 * @return bool
	 */
	public function post($data = '') {
		$request = $this->buildRequest();

		curl_setopt_array($request, array(
			CURLOPT_POST => TRUE,
			CURLOPT_POSTFIELDS => $data
		));

		return $this->executeRequest($request);
	}


	/**
	 * @return resource
	 */
	protected function buildRequest() {
		$request = curl_init($this->url);

		curl_setopt_array($request, array(
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_TIMEOUT => $this->timeOut,
		));

		if($this->proxyUrl) curl_setopt($request, CURLOPT_PROXY, $this->proxyUrl);
		if($this->cookieFilePath) curl_setopt($request, CURLOPT_COOKIEJAR, $this->cookieFilePath);

		return $request;
	}


	/**
	 * @param $request
	 * @return bool
	 */
	protected function executeRequest($request) {
		$this->result = curl_exec($request);
		return $this->handleResponse($request);
	}


	/**
	 * @param $request
	 * @return bool
	 */
	protected function handleResponse($request) {
		$requestSucceeded = TRUE;

		$this->httpCode = (int) curl_getinfo($request, CURLINFO_HTTP_CODE);

		if(curl_errno($request) > 0 || $this->httpCode !== 200) {
			$this->errorNumber = curl_errno($request);
			$this->errorMessage = curl_error($request);
			$requestSucceeded = FALSE;
		}

		curl_close($request);

		return $requestSucceeded;
	}


	/**
	 * @param string $url
	 * @return $this
	 */
	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}


	/**
	 * @param string $proxyUrl
	 * @return $this
	 */
	public function setProxyUrl($proxyUrl) {
		$this->proxyUrl = $proxyUrl;
		return $this;
	}


	/**
	 * @param integer $timeOut
	 * @return $this
	 */
	public function setTimeOut($timeOut) {
		$this->timeOut = $timeOut;
		return $this;
	}


	/**
	 * @param $cookieFilePath
	 */
	public function useCookiesFromFile($cookieFilePath) {
		$this->cookieFilePath = $cookieFilePath;
	}
} 