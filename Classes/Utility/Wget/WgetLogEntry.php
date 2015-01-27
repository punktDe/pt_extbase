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

namespace PunktDe\PtExtbase\Utility\Wget;


class WgetLogEntry {


	/**
	 * @var \DateTime
	 */
	protected $fetchDate;


	/**
	 * @var string
	 */
	protected $url;


	/**
	 * @var string
	 */
	protected $status;


	/**
	 * @var integer
	 */
	protected $statusCode;


	/**
	 * @var string
	 */
	protected $contentType;


	/**
	 * @var integer
	 */
	protected $contentLength;

	/**
	 * @return int
	 */
	public function getContentLength() {
		return $this->contentLength;
	}

	/**
	 * @param int $contentLength
	 */
	public function setContentLength($contentLength) {
		$this->contentLength = $contentLength;
	}

	/**
	 * @return string
	 */
	public function getContentType() {
		return $this->contentType;
	}

	/**
	 * @param string $contentType
	 */
	public function setContentType($contentType) {
		$this->contentType = $contentType;
	}

	/**
	 * @return \DateTime
	 */
	public function getFetchDate() {
		return $this->fetchDate;
	}

	/**
	 * @param \DateTime $fetchDate
	 */
	public function setFetchDate($fetchDate) {
		$this->fetchDate = $fetchDate;
	}

	/**
	 * @return string
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function getStatusCode() {
		return $this->statusCode;
	}

	/**
	 * @param int $statusCode
	 */
	public function setStatusCode($statusCode) {
		$this->statusCode = $statusCode;
	}

	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}
} 