<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 punkt.de GmbH
 *  Authors:
 *    Joachim Mathes <mathes@punkt.de>,
 *    Sascha Dörr <doerr@punkt.de>
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
 * BaseTestcase
 *
 * @package pt_extbase
 * @subpackage Testing\Selenium\FixtureFramework
 */
abstract class Tx_PtExtbase_Testing_Selenium_FixtureFramework_SeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase {

	/**
	 * @return array
	 */
	abstract function getFixtures();

	/**
	 * @return void
	 */
	protected function setUp() {
		parent::setUp();
		$fixtureImporter = new Tx_PtExtbase_Testing_Selenium_FixtureFramework_FixtureImporter();
		$fixtureImporter->import($this->getFixtures());
	}

}

?>