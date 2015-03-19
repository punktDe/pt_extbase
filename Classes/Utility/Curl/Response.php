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
	 * @var string
	 */
	protected $body;


	/**
	 * @var string
	 */
	protected $header;


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



	public function __construct($request, $resultData) {

		$this->errorNumber = curl_errno($request);
		$this->errorMessage = curl_error($request);

		$this->httpCode = (int) curl_getinfo($request, CURLINFO_HTTP_CODE);
		$this->headerSize = (int) curl_getinfo($request, CURLINFO_HEADER_SIZE);

		if($this->errorNumber > 0) {
			$this->requestSucceeded = FALSE;
		} else {
			$this->requestSucceeded = TRUE;
		}

		$this->processResult($resultData);

		curl_close($request);
	}


	/**
	 * @param $resultData
	 */
	protected function processResult($resultData) {
		$this->body = substr($resultData, $this->headerSize);
		$this->header = substr($resultData, 0, $this->headerSize);
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
	 * @return string
	 */
	public function getHeader() {
		return $this->header;
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
} 