<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
 *  Authors:
 *    Christian Herberger <herberger@punkt.de>,
 *    Ursula Klinger <klinger@punkt.de>,
 *    Daniel Lienert <lienert@punkt.de>,
 *    Joachim Mathes <mathes@punkt.de>
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
 * SQL Runner
 *
 * @package pt_extbase
 * @subpackage SqlRunner
 */
class Tx_PtExtbase_SqlRunner_Typo3SqlRunner implements Tx_PtExtbase_SqlRunner_SqlRunnerInterface {

	/**
	 * @var t3lib_db
	 */
	protected $connection;

	/**
	 * @var string
	 */
	protected $fileName;

	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->connection = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * @param string $fileName
	 * @return void
	 */
	public function runSqlFile($fileName) {
		$result = $this->connection->sql_query($this->loadSql());
		$this->connection->sql_free_result($result);
	}

	/**
	 * @return string
	 */
	protected function loadSql() {
		if (is_file($this->fileName)) {
			return file_get_contents($this->fileName);
		}
		return '';
	}

}
?>