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

namespace PunktDe\PtExtbase\Utility\Lock;


class MySqlLockStrategy implements LockStrategyInterface {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $connection;


	/**
	 * Identifier used for this lock
	 * @var string
	 */
	protected $identifier;


	/**
	 * The maximum time to acquire the lock
	 * @var integer
	 */
	protected $lockTime;


	public function __construct() {
		$this->connection = $GLOBALS['TYPO3_DB'];
		$this->lockTime = (int) ini_get('max_execution_time');
	}

	/**
	 * @param string $subject
	 * @param boolean $exclusiveLock TRUE to, acquire an exclusive (write) lock, FALSE for a shared (read) lock.
	 * @return boolean TRUE if an lock is acquired, FALSE if not
	 */
	public function acquire($subject, $exclusiveLock) {
		$this->identifier = $subject;
		$mysqliRes = $this->connection->sql_query(sprintf('SELECT GET_LOCK("%s", %d) AS res', $this->identifier, $this->lockTime));
		$resultArray = $mysqliRes->fetch_assoc();
		return $resultArray['res'];
	}


	/**
	 * @return boolean TRUE on success, FALSE otherwise
	 */
	public function release() {
		$this->connection->sql_query(sprintf('SELECT RELEASE_LOCK("%s") AS res', $this->identifier));
		return TRUE;
	}
}