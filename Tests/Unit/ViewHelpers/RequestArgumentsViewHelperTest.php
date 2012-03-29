<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Joachim Mathes <mathes@punkt.de>
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
 * RequestArgumentsViewHelper Test
 *
 * @package pt_extbase
 * @subpackage Tests\ViewHelpers
 */
class Tx_PtExtbase_Tests_Unit_ViewHelpers_RequestArgumnetsViewHelperTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	protected $proxyClass;

	protected $proxy;

	public function setUp() {
		$this->proxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_ViewHelpers_RequestArgumentsViewHelper');
		$this->proxy = new $this->proxyClass();
	}

	public function tearDown() {
		unset($this->proxy);
	}

	/**
	 * @test
	 */
	public function renderReturnsValidArgumentIfArgumentExists() {
		$viewHelperArguments = array(
			'key' => 'uid'
		);
		$expected = '35';

		$requestMock = $this->getMockBuilder('Tx_Extbase_MVC_Request')
				->setMethods(array('getArgument'))
				->getMock();
		$requestMock->expects($this->once())
			->method('getArgument')
			->will($this->returnValue($expected));

		$this->proxy->_set('arguments', $viewHelperArguments);
		$this->proxy->_set('request', $requestMock);

		$actual = $this->proxy->render();
		$this->assertEquals($expected, $actual);
	}

}
?>