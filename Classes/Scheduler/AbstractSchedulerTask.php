<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 punkt.de GmbH
 *  Authors:
 *    Michael Riedel <riedel@punkt.de>,
 *    Daniel Lienert <lienert@punkt.de>,
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

namespace PunktDe\PtExtbase\Scheduler;

use PunktDe\PtExtbase\Utility\TimeTracker;

/**
 * Abstract Scheduler Task
 * use for Time logging of a scheduler task
 *
 * @package pt_extbase
 * @subpackage Scheduler
 */
abstract class AbstractSchedulerTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {


	/**
	 * @var \Tx_PtExtbase_Logger_Logger
	 */
	private $logger;


	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->initializeExtbase();
		$this->initializeObject();
		parent::__construct();
	}

	/**
	 * @return void
	 */
	protected function initializeObject() {

	}



	/**
	 * Initialize Extbase
	 *
	 * This is necessary to resolve the TypoScript interface definitions
	 */
	protected function initializeExtbase() {
		$configuration['extensionName'] = $this->getExtensionName();
		$configuration['pluginName'] = 'dummy';

		$extbaseBootstrap = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Core\Bootstrap'); /** @var \TYPO3\CMS\Extbase\Core\Bootstrap $extbaseBootstrap */
		$extbaseBootstrap->initialize($configuration);

		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
		//$this->initializeObject();

	}



	/**
	 * Start a stopwatch
	 *
	 * @return integer Execution id
	 */
	public function markExecution() {
		TimeTracker::start('SchedulerTaskMeasure');
		//$this->initializeExtbase();

		return parent::markExecution();
	}



	/**
	 * Removes given execution from list
	 *
	 * @param integer $executionID Id of the execution to remove.
	 * @param \Exception $failure An exception to signal a failed execution
	 *
	 * @return    void
	 */
	public function unmarkExecution($executionID, \Exception $failure = NULL) {
		$this->logToApplicationLog();
		parent::unmarkExecution($executionID, $failure);
	}



	/**
	 * Return the extensionName for Extbase Initialization
	 *
	 * @return string
	 */
	abstract public function getExtensionName();



	/**
	 *
	 */
	protected function logToApplicationLog() {
		$this->logger = $this->objectManager->get('\Tx_PtExtbase_Logger_Logger');

		if ($this->logger instanceof \Tx_PtExtbase_Logger_Logger) {
			$usedTime = TimeTracker::stop('SchedulerTaskMeasure');
			$data['time'] = $usedTime;
			$taskTitle = trim($this->getTaskTitle());

			if ($taskTitle !== '') {
				$data['taskTitle'] = $taskTitle;
			}

			$this->logger->info(sprintf('Scheduler Task "%s" completed', $taskTitle, $usedTime), get_class($this), $data);
		}
	}
}
