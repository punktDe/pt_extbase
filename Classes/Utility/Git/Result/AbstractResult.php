<?php
namespace PunktDe\PtExtbase\Utility\Git\Result;

/***************************************************************
 *  Copyright (C) 2015 punkt.de GmbH
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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use PunktDe\PtExtbase\Utility\ShellCommandService;

/**
 * Abstract Result
 *
 * @package PunktDe\PtExtbase\Utility\Git\Result
 */
abstract class AbstractResult {

	/**
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;


	/**
	 * @inject
	 * @var \Tx_PtExtbase_Logger_Logger
	 */
	protected $logger;


	/**
	 * @inject
	 * @var \PunktDe\PtExtbase\Utility\Git\GitExecutionManager
	 */
	protected $gitExecutionManager;


	/**
	 * @var \PunktDe\PtExtbase\Utility\Git\Command\GitCommand
	 */
	protected $gitCommand;


	/**
	 * @var integer
	 */
	protected $exitCode;


	/**
	 * @var string
	 */
	protected $rawResult = '';


	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	protected $result;


	/**
	 * @param \PunktDe\PtExtbase\Utility\Git\Command\GitCommand $gitCommand
	 */
	public function __construct($gitCommand) {
		$this->gitCommand = $gitCommand;
	}


	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->result = $this->objectManager->get('TYPO3\CMS\Extbase\Persistence\ObjectStorage');
		$this->rawResult = $this->gitExecutionManager->execute($this->gitCommand);
	}


	/**
	 * @return int
	 */
	public function getExitCode() {
		return $this->exitCode;
	}



	/**
	 * @param int $exitCode
	 */
	public function setExitCode($exitCode) {
		$this->exitCode = $exitCode;
	}



	/**
	 * @return string
	 */
	public function getRawResult() {
		return $this->rawResult;
	}



	/**
	 * @param string $rawResult
	 */
	public function setRawResult($rawResult) {
		$this->rawResult = $rawResult;
	}



	/**
	 * @return ObjectStorage
	 */
	public function getResult() {
		return $this->result;
	}



	/**
	 * @return void
	 */
	public function __string() {
		echo $this->rawResult;
	}



	/**
	 * @return void
	 */
	abstract protected function buildResult();

}
