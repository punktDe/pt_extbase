<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
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
 * Class implements a testcase for the fake frontend creation
 *
 * @package Tests
 * @subpackage Utility
 * @author Daniel Lienert 
 */
class Tx_PtExtbase_Tests_Unit_Utility_FakeFrontendFactoryTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {


	public function setUp() {
		unset($GLOBALS['TSFE']);
	}


	/**
	 * @test
	 */
	public function classExists() {
		$this->assertTrue(class_exists('Tx_PtExtbase_Utility_FakeFrontendFactory'));
	}



	/**
	 * @test
	 */
	public function fakeFrontendCreatesGlobalsTsfe() {
		$this->assertNull($GLOBALS['TSFE']);

		/** @var $fakeFrontend Tx_PtExtbase_Utility_FakeFrontendFactory */
		$fakeFrontend = t3lib_div::makeInstance('Tx_PtExtbase_Utility_FakeFrontendFactory');
		$fakeFrontend->createFakeFrontEnd(1);

		$this->assertInstanceOf('tslib_fe', $GLOBALS['TSFE']);
	}


	/**
	 * @test
	 */
	public function fakeFrontendContainsCObj() {
		$this->assertNull($GLOBALS['TSFE']);

		/** @var $fakeFrontend Tx_PtExtbase_Utility_FakeFrontendFactory */
		$fakeFrontend = t3lib_div::makeInstance('Tx_PtExtbase_Utility_FakeFrontendFactory');
		$fakeFrontend->createFakeFrontEnd(1);

		$this->assertNotNull($GLOBALS['TSFE']->cObj, 'No Cobject in faked frontend.');
		$this->assertInstanceOf('tslib_cObj', $GLOBALS['TSFE']->cObj);
	}
	
}

?>