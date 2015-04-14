#!/usr/bin/env php
<?php

namespace PunktDe\PtExtbase\Tests\Functional\Utility\Lock;

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

class MySqlLockTestSecondInstance {

	/**
	 * @var \PDO
	 */
	protected $mySQLConnection;


	public function __construct() {
		$this->connect();
	}

	protected function connect() {

		// Load system specific configuration for Apache mode
		if (!isset($_SERVER['HTTP_HOST'])) $_SERVER['HTTP_HOST'] = $_SERVER['HOSTNAME'];
		$dpppConfiguration = __DIR__ . '/../../../../../../configurations/' . $_SERVER['HTTP_HOST'] . '.php';

		if (file_exists($dpppConfiguration)) {
			@include($dpppConfiguration);
		}

		$credentials = $GLOBALS['TYPO3_CONF_VARS']['DB'];

		$this->mySQLConnection = new \PDO('mysql:host=' . $credentials['host'] . ';dbname=' . $credentials['database'], $credentials['username'], $credentials['password']);

	}


	public function test() {

		if(!isset($_SERVER['argv']['1']) || !isset($_SERVER['argv']['2'])) {
			throw new \Exception('You have to specify the lock identifier and the testType', 1428853716);
		}

		$lockIdentifier = $_SERVER['argv']['1'];
		$testType = $_SERVER['argv']['2'];
		$timeout = $_SERVER['argv']['3'] ? $_SERVER['argv']['3'] : 5;

		switch($testType) {
			case 'acquireExclusiveLock':
				$this->testAcquireExclusiveLock($lockIdentifier, $timeout);
				break;
			case 'testIfLockIsFree':
				$this->testIfLockIsFree($lockIdentifier);
					break;
			default:
				throw new \Exception('No testMethod defined for ' . $testType);
		}

	}

	public function testIfLockIsFree($lockIndentifier) {
		$lockResult = $this->mySQLConnection->query(sprintf('SELECT IS_FREE_LOCK("%s") as res', $lockIndentifier))->fetch();
		echo $lockResult['res'];
	}

	public function testAcquireExclusiveLock($lockIdentifier, $timeout) {
		$lockResult = $this->mySQLConnection->query(sprintf('SELECT GET_LOCK("%s", %d) as res', $lockIdentifier, $timeout))->fetch();
		echo $lockResult['res'];
	}
}

$secondInstance = new MySqlLockTestSecondInstance();
$secondInstance->test();