<?php
namespace PunktDe\PtExtbase\Utility\Wget;

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


class WgetCommand {

	protected $argumentMap = array(
		'noCheckCertificate' => '--no-check-certificate',
		'convertLinks' => '--convert-links',
		'saveCookies' => '--save-cookies=%s',
		'loadCookies' => '--load-cookies=%s',
		'keepSessionCookies' => '--keep-session-cookies',
		'execute' => '--execute %s',
		'tries' => '--tries=%s',
		'retryConnRefused' => '--retry-connrefused',
		'serverResponse' => '--server-response',
		'directoryPrefix' => '--directory-prefix=%s',
		'domains' => '--domains=%s',
		'pageRequisites' => '--page-requisites',
		'outputFile' => '--output-file=%s'
	);


	/**
	 * @var string
	 */
	protected $wgetBinaryPath = '/usr/local/bin/wget';


	/**
	 * @var string
	 */
	protected $url;


	/**
	 * @var Boolean
	 */
	protected $noCheckCertificate;


	/**
	 * @var Boolean
	 */
	protected $convertLinks;


	/**
	 * @var string
	 */
	protected $saveCookies;


	/**
	 * @var string
	 */
	protected $loadCookies;


	/**
	 * @var Boolean
	 */
	protected $keepSessionCookies;


	/**
	 * @var Integer
	 */
	protected $tries;


	/**
	 * Execute command as if it were a part of .wgetrc
	 *
	 * @var string
	 */
	protected $execute;


	/**
	 * Consider “connection refused” a transient error and try again.
	 * Normally Wget gives up on a URL when it is unable to connect to the site because
	 * failure to connect is taken as a sign that the server is not running at all and that retries would not help.
	 * This option is for mirroring unreliable sites whose servers tend to disappear for short periods of time.
	 *
	 * @var Boolean
	 */
	protected $retryConnRefused;


	/**
	 * Print the headers sent by HTTP servers and responses sent by FTP servers.
	 *
	 * @var Boolean
	 */
	protected $serverResponse;


	/**
	 * @var String
	 */
	protected $directoryPrefix;


	/**
	 * @var String
	 */
	protected $domains;


	/**
	 * This option causes Wget to download all the files that are necessary to properly display a given HTML page.
	 * This includes such things as inlined images, sounds, and referenced stylesheets.
	 *
	 * @var String
	 */
	protected $pageRequisites;


	/**
	 * @var string
	 */
	protected $outputFile;


	/**
	 * @param string $wgetBinaryPath
	 * @return $this
	 */
	public function setWgetBinaryPath($wgetBinaryPath) {
		$this->wgetBinaryPath = $wgetBinaryPath;
		return $this;
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
	 * @param String $directoryPrefix
	 * @return $this
	 */
	public function setDirectoryPrefix($directoryPrefix) {
		$this->directoryPrefix = $directoryPrefix;
		return $this;
	}


	/**
	 * @param boolean $convertLinks
	 * @return $this
	 */
	public function setConvertLinks($convertLinks) {
		$this->convertLinks = $convertLinks;
		return $this;
	}


	/**
	 * @param string $outputFile
	 * @return $this
	 */
	public function setOutputFile($outputFile) {
		$this->outputFile = $outputFile;
		return $this;
	}


	/**
	 * @param String $domains
	 * @return $this
	 */
	public function setDomains($domains) {
		$this->domains = $domains;
		return $this;
	}


	/**
	 * @param string $execute
	 * @return $this
	 */
	public function setExecute($execute) {
		$this->execute = $execute;
		return $this;
	}


	/**
	 * @param Booelan $keepSessionCookies
	 * @return $this
	 */
	public function setKeepSessionCookies($keepSessionCookies) {
		$this->keepSessionCookies = $keepSessionCookies;
		return $this;
	}


	/**
	 * @param boolean $noCheckCertificate
	 * @return $this
	 */
	public function setNoCheckCertificate($noCheckCertificate) {
		$this->noCheckCertificate = $noCheckCertificate;
		return $this;
	}


	/**
	 * @param Boolean $pageRequisites
	 * @return $this
	 */
	public function setPageRequisites($pageRequisites) {
		$this->pageRequisites = $pageRequisites;
		return $this;
	}


	/**
	 * @param boolean $retryConnRefused
	 * @return $this
	 */
	public function setRetryConnRefused($retryConnRefused) {
		$this->retryConnRefused = $retryConnRefused;
		return $this;
	}


	/**
	 * @param string $saveCookies
	 * @return $this
	 */
	public function setSaveCookies($saveCookies) {
		$this->saveCookies = $saveCookies;
		return $this;
	}



	/**
	 * @param boolean $serverResponse
	 * @return $this
	 */
	public function setServerResponse($serverResponse) {
		$this->serverResponse = $serverResponse;
		return $this;
	}


	/**
	 * @param int $tries
	 * @return $this
	 */
	public function setTries($tries) {
		$this->tries = $tries;
		return $this;
	}


	/**
	 * @param String $loadCookies
	 * @return $this
	 */
	public function setLoadCookies($loadCookies) {
		$this->loadCookies = $loadCookies;
		return $this;
	}





	/**
	 * @return string
	 */
	protected function buildCommand() {

		$arguments = array();

		foreach($this->argumentMap as $propertyName => $argumentTemplate) {
			if(property_exists($this, $propertyName) && !empty($this->$propertyName) && $this->$propertyName !== FALSE) {
				if(stristr($argumentTemplate, '%s') === FALSE) {
					$arguments[] = $argumentTemplate;
				} else {
					$arguments[] = sprintf($argumentTemplate, $this->$propertyName);
				}
			}
		}

		return sprintf('%s %s %s', $this->wgetBinaryPath, implode(' ', $arguments), $this->url);
	}


	/**
	 * @return string
	 */
	public function getCommand() {
		return $this->buildCommand();
	}


	/**
	 * Executes the wget command
	 */
	public function execute() {
		exec($this->buildCommand());
	}
} 