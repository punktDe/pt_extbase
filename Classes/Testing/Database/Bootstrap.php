<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
 *  Authors:
 *	  Joachim Mathes <mathes@punkt.de>
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

/**
 * Database Test Bootstrap
 *
 * @package pt_extbase
 * @subpackage Testing\Database
 */
class Tx_PtExtbase_Testing_Database_Bootstrap {

	/**
	 * @return string
	 */
	public function getTestDatabaseDsn() {
		if (isset($GLOBALS['TEST_DATABASE_DSN'])) {
			return $GLOBALS['TEST_DATABASE_DSN'];
		}
	}

	/**
	 * @return string
	 */
	public function getTestDatabaseHostname() {
		if (isset($GLOBALS['TEST_DATABASE_HOSTNAME'])) {
			return $GLOBALS['TEST_DATABASE_HOSTNAME'];
		}
	}

	/**
	 * @return string
	 */
	public function getTestDatabaseUsername() {
		if (isset($GLOBALS['TEST_DATABASE_USERNAME'])) {
			return $GLOBALS['TEST_DATABASE_USERNAME'];
		}
	}

	/**
	 * @return string
	 */
	public function getTestDatabasePassword() {
		if (isset($GLOBALS['TEST_DATABASE_PASSWORD'])) {
			return $GLOBALS['TEST_DATABASE_PASSWORD'];
		}
	}

	/**
	 * @return string
	 */
	public function getTestDatabaseSchema() {
		if (isset($GLOBALS['TEST_DATABASE_SCHEMA'])) {
			return $GLOBALS['TEST_DATABASE_SCHEMA'];
		}
	}

}

?>