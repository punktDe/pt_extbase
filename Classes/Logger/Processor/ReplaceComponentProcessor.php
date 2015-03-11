<?php

namespace PunktDe\PtExtbase\Logger\Processor;

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

use \TYPO3\CMS\Core\Log\LogRecord;
use \TYPO3\CMS\Core\Log\Processor\AbstractProcessor;

/**
 * Replace Component Processor
 *
 * This processor replaces the component of a log message by the first entry
 * of the logger data. This is a convention which is used by the pt_extbase
 * logger to allow different components within one logger.
 *
 * @package pt_extbase
 */
class ReplaceComponentProcessor extends AbstractProcessor {

	/**
	 * @param LogRecord $logRecord
	 * @return LogRecord
	 */
	public function processLogRecord(LogRecord $logRecord) {
		$data = $logRecord->getData();

		if (array_key_exists('loggerComponent', $data)) {
			$logRecord->setComponent($data['loggerComponent']);
			unset($data['loggerComponent']);
		}

		$logRecord->setData($data);

		return $logRecord;
	}

}