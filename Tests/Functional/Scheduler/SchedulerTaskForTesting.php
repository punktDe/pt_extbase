<?php

namespace PunktDe\PtDpppFis\Scheduler;
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


use PunktDe\PtExtbase\Scheduler\AbstractSchedulerTask;

/**
 * Class IndexTask
 *
 * @package PunktDe\PtDpppFis\Scheduler
 */
class IndexTask extends AbstractSchedulerTask {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;


	/**
	 * @var \PunktDe\PtDpppFis\Domain\Archiver\Archiver
	 */
	protected $archiver;


	/**
	 * @var \PunktDe\PtDpppMultipleDatabases\XClass\DatabaseConnection
	 */
	protected $databaseConnection;

	/**
	 * @var \Tx_PtExtbase_Logger_Logger
	 */
	protected $logger;

	/**
	 * @return boolean
	 * @throws \Exception
	 */
	public function execute() {
		try {
			$this->archiver->archive($this->getLastExecutionTime());

			return TRUE;
		} catch (\Exception $e) {
			$this->logger->error(sprintf('%s (%s)', $e->getMessage(), $e->getCode()));
			throw $e;
		}
	}


	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		$this->logger = $this->objectManager->get('Tx_PtExtbase_Logger_Logger');
		$this->archiver = $this->objectManager->get('PunktDe\PtDpppFis\Domain\Archiver\Archiver');
		$this->databaseConnection = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * @return mixed
	 */
	protected function getLastExecutionTime() {
		$result = $this->databaseConnection->exec_SELECTgetSingleRow('tstamp', 'tx_ptdpppfis_domain_model_version', 'status = "' . \PunktDe\PtDpppFis\Domain\Model\Version::STATUS_SUCCESS . '"', '', 'tstamp DESC');

		return $result['tstamp'];
	}

	/**
	 * Return the extensionName for Extbase Initialization
	 *
	 * @return string
	 */
	public function getExtensionName() {
		return 'PtDpppFis';
	}
}
