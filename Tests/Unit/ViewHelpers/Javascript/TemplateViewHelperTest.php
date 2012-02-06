<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 Daniel Lienert <daniel@lienert.cc>, Michael Knoll <knoll@punkt.de>
*  All rights reserved
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
 * Testcase for JavaScript TemplateViewHelper
 *
 * @package pt_extbase
 * @subpackage Tests\ViewHelpers\Javascript
 * @author Daniel Lienert <daniel@lienert.cc>
 * @author Joachim Mathes <mathes@punkt.de>
 */
class Tx_PtExtbase_Tests_Unit_ViewHelpers_Javascript_TemplateViewhelperTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	protected $accessibleProxyClass;

	/**
	 * @var Tx_PtExtbase_ViewHelpers_Javascript_TemplateViewHelper
	 */
	protected $accessibleProxy;

	public function setUp() {
		$this->accessibleProxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_ViewHelpers_Javascript_TemplateViewHelper');
		$this->accessibleProxy = new $this->accessibleProxyClass();
	}

	public function tearDown() {
		unset($this->accessibleProxy);
	}

	public function testPrepareMarkersBuildsArrayWithPrefixedAndPostfixedKeys() {
		$input = array(
			'foo' => 'bar',
			'bar' => 'baz'
		);
		$expected = array(
			'###foo###' => 'bar',
			'###bar###' => 'baz'
		);
		$actual = $this->accessibleProxy->_call('prepareMarkers', $input);
		$this->assertEquals($expected, $actual);
	}

	public function testAddTranslationArguments() {
		$accessibleProxyMock = $this->getMockBuilder($this->accessibleProxyClass)
				->setMethods(array('translate'))
				->getMock();
		$accessibleProxyMock->expects($this->once())
				->method('translate')
				->will($this->returnValue('bar'));
		$accessibleProxyMock->_set('extKey', 'PtExtbase');

		$input = "
		dialog({
            autoOpen: false,
            modal: true,
	         // ###translate###
	            title: '###LLL:foo###'
	        });
		";
		$expected = array('###LLL:foo###' => 'bar');
		$actual = array();
		$accessibleProxyMock->_callRef('addTranslationMarkers', $input, $actual);
		$this->assertEquals($expected, $actual);
	}

	public function testSubstituteMarkers() {
		$input = "
		dialog({
            autoOpen: false,
            modal: true,
	         // ###bar###
	            title: '###LLL:foo###'
	        });
		";
		$expected = "
		dialog({
            autoOpen: false,
            modal: true,
	         // baz
	            title: 'bar'
	        });
		";
		$markers = array(
			'###bar###' => 'baz',
			'###LLL:foo###' => 'bar',
		);

		$accessibleProxyMock = $this->getMockBuilder($this->accessibleProxyClass)
				->setMethods(array('prepareMarkers', 'addTranslationMarkers'))
				->getMock();
		$accessibleProxyMock->expects($this->once())
				->method('prepareMarkers')
				->will($this->returnValue($markers));
		$accessibleProxyMock->expects($this->once())
				->method('addTranslationMarkers');

		$actual = $accessibleProxyMock->_call('substituteMarkers', $input, $markers);
		$this->assertEquals($expected, $actual);
	}

}

?>