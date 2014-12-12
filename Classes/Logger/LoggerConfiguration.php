<?php
/***************************************************************
 *  Copyright (C) 2014 punkt.de GmbH
 *  Authors: el_equipo <opiuqe_le@punkt.de>
 *
 *  This script is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use \TYPO3\CMS\Core\Log\LogLevel;
use \TYPO3\CMS\Core\SingletonInterface;

/**
 * Logger Configuration
 */
class Tx_PtExtbase_Logger_LoggerConfiguration implements SingletonInterface {

	/**
	 * @var array
	 */
	protected $extensionConfiguration = array();


	/**
	 * @var string
	 */
	protected $logFilePath;


	/**
	 * @var string
	 */
	protected $exceptionDirectory;


	/**
	 * @var integer
	 */
	protected $logLevelThreshold = LogLevel::INFO;


	/**
	 * @var integer
	 */
	protected $emailLogLevelThreshold = LogLevel::CRITICAL;


	/**
	 * @var string
	 */
	protected $emailReceivers = '';


	/**
	 * @return Tx_PtExtbase_Logger_LoggerConfiguration
	 * @throws Tx_PtExtbase_Exception_Exception
	 */
	public function __construct() {
		$this->extensionConfiguration = Tx_PtExtbase_Div::returnExtConfArray('pt_extbase');
		$this->evaluateLogFilePath();
		$this->evaluateExceptionDirectory();
		$this->setLogLevelThresholdByExtensionConfigurationProperty('logLevelThreshold');
		$this->setLogLevelThresholdByExtensionConfigurationProperty('emailLogLevelThreshold');
		$this->evaluateEmailReceivers();
	}



	/**
	 * @return void
	 */
	protected function evaluateLogFilePath() {
		if(array_key_exists('logFilePath', $this->extensionConfiguration)) {
			$this->logFilePath = $this->extensionConfiguration['logFilePath'];
		} else {
			$this->logFilePath = Tx_PtExtbase_Utility_Files::concatenatePaths(array(PATH_site, '/typo3temp/application.log'));
		}

		if(!file_exists($this->logFilePath)){
			echo 'The configured Log File Path "' . $this->logFilePath .'" doesn\'t exist';
		}
	}



	/**
	 * @return void
	 */
	protected function evaluateExceptionDirectory() {
		if(!$this->exceptionDirectory) {
			$path_parts = pathinfo($this->logFilePath);

			$this->exceptionDirectory = Tx_PtExtbase_Utility_Files::concatenatePaths(array(realpath($path_parts['dirname']), 'Exceptions'));
			Tx_PtExtbase_Utility_Files::createDirectoryRecursively($this->exceptionDirectory);
		}
	}



	/**
	 * @param string $property
	 * @return integer|NULL
	 */
	protected function setLogLevelThresholdByExtensionConfigurationProperty($property) {
		if(array_key_exists($property, $this->extensionConfiguration)) {
			TYPO3\CMS\Core\Log\LogLevel::validateLevel($this->extensionConfiguration[$property]);
			$this->$property =  (integer) $this->extensionConfiguration[$property];
		}
	}



	/**
	 * @return void
	 *
	 * TODO: Add some logic to check if e-mail addresses are syntactically valid
	 */
	protected function evaluateEmailReceivers() {
		if(array_key_exists('emailReceivers', $this->extensionConfiguration)) {
			$this->emailReceivers =  $this->extensionConfiguration['emailReceivers'];
		}
	}



	/**
	 * @return int
	 */
	public function getEmailLogLevelThreshold() {
		return $this->emailLogLevelThreshold;
	}



	/**
	 * @return string
	 */
	public function getExceptionDirectory() {
		return $this->exceptionDirectory;
	}



	/**
	 * @return string
	 */
	public function getLogFilePath() {
		return $this->logFilePath;
	}



	/**
	 * @return int
	 */
	public function getLogLevelThreshold() {
		return $this->logLevelThreshold;
	}



	/**
	 * @return string
	 */
	public function getEmailReceivers() {
		return $this->emailReceivers;
	}



	/**
	 * @return boolean
	 */
	public function weHaveAnyEmailReceivers() {
		if ($this->emailReceivers === '') {
			return FALSE;
		}
		return TRUE;
	}

}