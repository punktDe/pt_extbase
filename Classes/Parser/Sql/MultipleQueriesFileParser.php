<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 punkt.de GmbH
 *  Authors:
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
 * Multiple Queries File Parser
 *
 * This is a very simple parser for files containing multiple SQL queries.
 * Since mysql_query() is not able to execute multiple SQL queries, it is necessary to split
 * them before.
 *
 * @package pt_extbase
 * @subpackage Parser\Sql
 */
class Tx_PtExtbase_Parser_Sql_MultipleQueriesFileParser {

	/**
	 * @param string $filePath
	 * @return array of queries
	 */
	public function parse($filePath) {
		$queries = array();
		$lines = $this->loadSqlFile($filePath);
		$query = '';
		foreach($lines as $line){
			if($this->isValidLine($line)) {
				$query .= $line;
				if ($this->isEndOfQuery($query)) {
					$queries[] = $query;
					$query = '';
				}
			}
		}
		return $queries;
	}

	/**
	 * @param string $filePath
	 * @return array
	 * @throws Exception
	 */
	protected function loadSqlFile($filePath) {
		if (is_file($filePath)) {
			return file($filePath);
		}
		throw new Exception('Not a valid file: ' . $filePath . '! 1347035058');
	}

	/**
	 * @param string $line
	 * @return boolean
	 */
	protected function isValidLine($line) {
		if (trim($line) != '' && strpos($line, '--') === FALSE) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * @param string $query
	 * @return boolean
	 */
	protected function isEndOfQuery($query) {
		if (substr(rtrim($query), -1) == ';') {
			return TRUE;
		}
		return FALSE;
	}

}
?>