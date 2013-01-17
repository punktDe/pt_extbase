<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
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
 * Class implements a base testcase for pt_extbase testcases
 *
 * @package Tests
 * @author Daniel Lienert
 * @author Michael Knoll
 */
abstract class Tx_PtExtbase_Tests_Unit_AbstractModelTestcase extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/**
	 * Holds the (accessible proxy-) object, which will be tested.
	 *
	 * @var mixed
	 */
	protected $proxy;

	/**
	 * @var array
	 */
	protected $settableAttributes = array();

	/**
	 * Instead of annoying getter setter test we test dem automatically by just providing the
	 * attributes to be tested
	 *
	 * @return array
	 */
	public function getterSetterTestDataProvider() {
		$testData = array();

		foreach($this->settableAttributes as $attribute => $type) {
			switch ($type) {
				case 'string':
					$testValue = uniqid('testString');
					break;

				case 'int':
					$testValue = rand(-10000, +10000);
					break;

				case 'boolean':
					$testValue = TRUE;
					break;
			}

			$testName = 'Test setter and getter for Attribute ' . $attribute;
			$getterName = 'get' . ucfirst($attribute);
			$setterName = 'set' . ucfirst($attribute);

			$testData[$testName] = array('setterName' => $setterName, 'getterName' => $getterName, 'testValue' => $testValue);
		}

		return $testData;
	}


	/**
	 * @param string $setterName
	 * @param string $getterName
	 * @param mixed $testValue
	 *
	 * @test
	 * @dataProvider getterSetterTestDataProvider
	 */
	public function getterSetterTest($setterName, $getterName, $testValue) {
		$this->assertTrue(method_exists($this->proxy, $getterName), 'No getter named ' . $getterName . ' accessible.');
		$this->assertTrue(method_exists($this->proxy, $setterName), 'No setter named ' . $setterName . ' accessible.');

		$this->proxy->$setterName($testValue);

		$this->assertSame($testValue,
			$this->proxy->$getterName(),
			'TestValue ' . $testValue . ' was set, but ' . $this->proxy->$getterName() . ' was returned from the model.'
		);
	}

}
?>