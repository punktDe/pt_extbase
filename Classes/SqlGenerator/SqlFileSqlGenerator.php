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
 * SQL-File SQL Generator
 *
 * @package pt_extbase
 * @subpackage SqlGenerator
 */
class Tx_PtExtbase_SqlGenerator_SqlFileSqlGenerator implements Tx_PtExtbase_SqlGenerator_SqlGeneratorCommandInterface {

	/**
	 * @var Tx_PtExtbase_Parser_Sql_MultipleQueriesFileParser
	 */
	protected $multipleQueriesFileParser;

	/**
	 * @param Tx_PtExtbase_Parser_Sql_MultipleQueriesFileParser $multipleQueriesFileParser
	 * @return void
	 */
	public function injectMultipleQueriesFileParser(Tx_PtExtbase_Parser_Sql_MultipleQueriesFileParser $multipleQueriesFileParser) {
		$this->multipleQueriesFileParser = $multipleQueriesFileParser;
	}

	/**
	 * @param string $filePath
	 * @return array
	 */
	public function generate($filePath) {
		return $this->multipleQueriesFileParser->parse($filePath);
	}

}
?>