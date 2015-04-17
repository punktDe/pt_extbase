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
	 *
	 * @throws LockNotAcquiredException
	 * @throws \Exception
	 */
	public function acquire($subject, $exclusiveLock) {
		if (!$exclusiveLock) {
			throw new \Exception('Shared lock is not possible when using MySqlLockStrategy', 1429016835);
		}

		$this->identifier = $subject;

		$isFreeLockRes = $this->connection->sql_query(sprintf('SELECT IS_FREE_LOCK("%s") AS res', $this->identifier))->fetch_assoc();
		if ((int) $isFreeLockRes['res'] !== 1) {
			throw new LockNotAcquiredException(sprintf('Lock %s is already acquired', $this->identifier), 1429016827);
		}

		$getLockRes = $this->connection->sql_query(sprintf('SELECT GET_LOCK("%s", %d) AS res', $this->identifier, $this->lockTime))->fetch_assoc();
		if (!$getLockRes['res']) {
			throw new LockNotAcquiredException(sprintf('Lock %s could not be acquired after waiting %d ms', $this->identifier, $this->lockTime), 1429016830);
		}

		return TRUE;
	}


	/**
	 * @return boolean TRUE on success, FALSE otherwise
	 */
	public function release() {
		$res = $this->connection->sql_query(sprintf('SELECT RELEASE_LOCK("%s") AS res', $this->identifier));
		if($res) {
			$releaseLockRes = $res->fetch_assoc();
			return ((int) $releaseLockRes['res']) === 1;
		}

		return FALSE;
	}
}