<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert
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
 * Unit test for abstract api controller
 * 
 * @author Daniel Lienert
 * @package Tests
 * @subpackage Controllers
 */
class Tx_PtExtbase_Tests_Unit_Controller_AbstractApiControllerTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/**
	 * @var Tx_PtExtbase_Controller_AbstractApiController
	 */
	protected $controller;


	public function setUp() {
		$proxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Controller_AbstractApiController');
		$this->controller = $this->objectManager->get($proxyClass);
	}


	/**
	 * @test
	 */
	public function findFirstError() {

		/*
		 * Root
		 * 	- A
		 * 	- B
		 * 		- C (With Error)
		 */
		$rootResult = new \TYPO3\CMS\Extbase\Error\Result();
		$rootResult->forProperty('A');
		$rootResult->forProperty('B')->forProperty('C')->addError(new \TYPO3\CMS\Extbase\Error\Error('Fehler', 123456));


		$foundError = $this->controller->_call('findFirstError', $rootResult); /** @var \TYPO3\CMS\Extbase\Error\Error $foundError */

		$this->assertInstanceOf('\TYPO3\CMS\Extbase\Error\Error', $foundError);
		$this->assertEquals(123456, $foundError->getCode());
	}

}

