<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Joachim Mathes <mathes@punkt.de>
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
 * StringToLowerViewHelper Test
 *
 * @package pt_extbase
 * @subpackage Tests\ViewHelpers\Format
 */
class Tx_PtExtbase_Tests_Unit_ViewHelpers_Format_StringToLowerViewHelperTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	protected $stringToLowerViewHelperProxyClass;

	protected $stringToLowerViewHelperProxy;

	public function setUp() {
		$this->stringToLowerViewHelperProxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_ViewHelpers_Format_StringToLowerViewHelper');
		$this->stringToLowerViewHelperProxy = new $this->stringToLowerViewHelperProxyClass();
	}

	public function testRenderIfInputValueIsString() {
		$viewHelperArguments = array(
			'string' => 'BaR'
		);
		$expectedOutput = 'bar';
		$this->stringToLowerViewHelperProxy->_set('arguments', $viewHelperArguments);
		$output = $this->stringToLowerViewHelperProxy->render();
		$this->assertEquals($expectedOutput, $output);
	}

	public function testRenderReturnsInputValueIfInputValueIsNotString() {
		$viewHelperArguments = array(
			'string' => 123
		);
		$expectedOutput = 123;
		$this->stringToLowerViewHelperProxy->_set('arguments', $viewHelperArguments);
		$output = $this->stringToLowerViewHelperProxy->render();
		$this->assertEquals($expectedOutput, $output);
	}

	public function tearDown() {
		unset($this->stringToLowerViewHelperProxy);
	}
	
}
?>