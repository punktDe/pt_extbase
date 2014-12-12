<?php
namespace PunktDe\PtExtbase\Logger;

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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Log\LogManager;

/**
 *  Logger Manager
 *
 * @package pt_extbase
 */
class LoggerManager extends LogManager {

	/**
	 * @var array|NULL
	 */
	protected $loggerConfiguration;


	/**
	 * @return LoggerManager
	 */
	public function __construct() {
		parent::__construct();
		$this->loggerConfiguration = $GLOBALS['TYPO3_CONF_VARS']['LOG'];
	}



	/**
	 * Gets a logger instance for the given name.
	 *
	 * This method overrides the TYPO3 core logger method to reduce the number
	 * of instantiated loggers. This is done by grouping loggers by their
	 * available configuration.
	 *
	 * @param string $name Logger name, empty to get the global "root" logger.
	 * @return \TYPO3\CMS\Core\Log\Logger Logger with name $name
	 */
	public function getLogger($name = '') {
		$logger = NULL; /** @var $logger \TYPO3\CMS\Core\Log\Logger */

		$componentName = $this->unifyComponentName($name);
		$indexName = $this->evaluateIndexNameByComponentName($componentName);

		if (isset($this->loggers[$indexName])) {
			$logger = $this->loggers[$indexName];
		} else {
			$logger = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\Logger', $indexName);
			$this->loggers[$indexName] = $logger;
			$this->setWritersForLogger($logger);
			$this->setProcessorsForLogger($logger);
		}

		return $logger;
	}



	/**
	 * @param $componentName
	 * @return string
	 */
	protected function evaluateIndexNameByComponentName($componentName) {
		$indexNameParts = array();

		$explodedName = explode('.', $componentName);
		$configuration = $this->loggerConfiguration;

		foreach ($explodedName as $partOfClassName) {
			if (!empty($configuration[$partOfClassName])) {
				$indexNameParts[] = $partOfClassName;
			}
			$configuration = $configuration[$partOfClassName];
		}

		return implode('.', $indexNameParts);
	}



	/**
	 * 	Transform namespaces and underscore class names to the dot-name style
	 *
	 * @param $componentName
	 * @return string
	 */
	protected function unifyComponentName($componentName) {
		$separators = array('_', '\\');
		return str_replace($separators, '.', $componentName);
	}

}