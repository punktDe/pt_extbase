<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
 *
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
 * Testcase for header column class
 * 
 * @author Daniel Lienert 
 * @package Tests
 */
class Tx_PtExtbase_Tests_DebugCodeTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var string Put the extension name here
	 */
	protected $extensionName = 'pt_extbase';


	/**
	 * @return array
	 */
	public function debugStringDataProvider() {
		return array(
			'Search for print_r in code!' => array('debugCommand' => 'print_r'),
			'Search for var_dump in code!' => array('debugCommand' => 'var_dump'),
		);
	}

	/**
	 * @test
	 * @dataProvider debugStringDataProvider
	 * 
	 * @var $debugCommand
	 */
	public function checkForForgottenDebugCode($debugCommand) {
		//$this->markTestSkipped('Test skipped, since grep finds the provided strings in this class.');
		$searchPath = t3lib_extMgm::extPath($this->extensionName);

		$command = 'fgrep -i -r ' . escapeshellarg($debugCommand) . ' ' . escapeshellarg($searchPath) . '| grep ".php"';
		$result = `$command`;
		$lines = explode("\n", trim($result));

		foreach($lines as $line) {
			if(!stristr($line, __FILE__) && !$this->checkForReturningPrintR($line)) {
				$this->fail('Found ' . $debugCommand . ': ' . $line);
			}
		}
	}


	/**
	 * Checks if a print_r consists in the line and returns true if this print_r
	 * is configured to return the array representation instead of displaying it.
	 *
	 * @param $line
	 * @return bool
	 */
	protected function checkForReturningPrintR($line) {
		if(!stristr($line, 'print_r')) return false;

		$printRArgumentTemp = explode('print_r(', $line);
		$printRArgumentTemp = explode(')',$printRArgumentTemp[1]);
		$printRArgumentTemp = explode(',', $printRArgumentTemp[0]);
		
		if(in_array(trim(strtolower($printRArgumentTemp[1])),array('1','true'))) {
			return true;
		}

		return false;
	}
}
?>