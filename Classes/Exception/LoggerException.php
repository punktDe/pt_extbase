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

/**
 * InfoLoggerException
 *
 * @package pt_dppp_esales
 */
class Tx_PtExtbase_Exception_LoggerException extends Exception {

	/**
	 * @var int
	 * @see t3lib_log_Level
	 */
	protected $logLevel;


	/**
	 * @param string $message
	 * @param int $code
	 * @param \Exception|int $logLevel
	 * @param Exception $previous
	 */
	public function __construct($message = "", $code = 0, $logLevel = t3lib_log_Level::ERROR, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
		if (t3lib_log_Level::isValidLevel($logLevel)) {
			$this->logLevel = $logLevel;
		} else {
			$this->logLevel = t3lib_log_Level::ERROR;
		}
	}


	/**
	 * @return int
	 */
	public function getLogLevel() {
		return $this->logLevel;
	}

}
