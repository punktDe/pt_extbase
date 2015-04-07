<?php
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

namespace PunktDe\PtExtbase\Utility\Curl;


class Response {

	/**
	 * @var integer
	 */
	protected $httpCode;


	/**
	 * Defines which status codes are errors
	 *
	 * @var array
	 */
	protected $errorStatusPattern = '[4,5]..';


	/**
	 * @var string
	 */
	protected $body;


	/**
	 * @var string
	 */
	protected $header = array();


	/**
	 * @var integer
	 */
	protected $headerSize;


	/**
	 * @var boolean
	 */
	protected $requestSucceeded = FALSE;


	/**
	 * @var integer
	 */
	protected $errorNumber;


	/**
	 * @var string
	 */
	protected $errorMessage;


	/**
	 * @var integer
	 */
	protected $requestTime;

	/**
	 * @var Request
	 */
	protected $request;


	/**
	 * @param $requestHandle
	 * @param Request $request
	 * @param $resultData
	 */
	public function __construct($requestHandle, Request $request, $resultData) {

		$this->request = $request;

		$this->errorNumber = curl_errno($requestHandle);
		$this->errorMessage = curl_error($requestHandle);

		$this->httpCode = (int) curl_getinfo($requestHandle, CURLINFO_HTTP_CODE);
		$this->headerSize = (int) curl_getinfo($requestHandle, CURLINFO_HEADER_SIZE);
		$this->requestTime = (int) curl_getinfo($requestHandle, CURLINFO_TOTAL_TIME);

		if($this->errorNumber > 0 || preg_match(sprintf('/%s/', $this->errorStatusPattern), (string) $this->httpCode) != 0) {
			$this->requestSucceeded = FALSE;
		} else {
			$this->requestSucceeded = TRUE;
		}

		$this->processResult($resultData);

		curl_close($requestHandle);
	}


	/**
	 * @param $resultData
	 */
	protected function processResult($resultData) {

		if($this->request->getCurlOptions(CURLOPT_PROXY) !== NULL) {
			$this->stripProxyHeader($resultData);
		}

		list($headerText, $this->body) = explode("\r\n\r\n", $resultData, 2);

		foreach (explode("\r\n", $headerText) as $i => $headerLine) {
			if ($i === 0) {
				$this->header['http_code'] = $headerLine;
			} else {
				list ($key, $value) = explode(': ', $headerLine);
				$this->header[$key] = $value;
			}
		}
	}


	/**
	 * @param string $resultData
	 * @return string
	 */
	protected function stripProxyHeader(&$resultData) {
		// cURL automatically handles Proxy rewrites, remove the "HTTP/X.X *" string
		$resultData = preg_replace("/HTTP\/\d.\d\s.*\r\n\r\n/", '', $resultData);
		return $resultData;
	}

	/**
	 * @return int
	 */
	public function getErrorNumber() {
		return $this->errorNumber;
	}

	/**
	 * @return string
	 */
	public function getErrorMessage() {
		return $this->errorMessage;
	}

	/**
	 * @return int
	 */
	public function getHttpCode() {
		return $this->httpCode;
	}

	/**
	 * @return int
	 */
	public function getRequestTime() {
		return $this->requestTime;
	}


	/**
	 * @param string $key
	 * @return string|array
	 */
	public function getHeader($key = '') {
		if($key === '') {
			return $this->header;
		} else {
			if(array_key_exists($key, $this->header)) {
				return $this->header[$key];
			}
		}

		return '';
	}


	/**
	 * @return string
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @return bool
	 */
	public function isRequestSucceeded() {
		return $this->requestSucceeded;
	}

	/**
	 * @param Request $request
	 */
	public function setRequest($request) {
		$this->request = $request;
	}


	/**
	 * @return Request
	 */
	public function getRequest() {
		return $this->request;
	}
}