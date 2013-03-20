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
 * Test case for class Tx_PtExtbase_SqlGenerator_SqlGenerator
 *
 * @package pt_extbase
 * @subpackage Tests\Unit\Parser\Sql
 */
class Tx_PtExtbase_Tests_Unit_SqlGenerator_SqlGeneratorTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	protected $proxyClass;

	protected $proxy;

	public function setUp() {
		$this->proxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_SqlGenerator_SqlGenerator');
		$this->proxy = new $this->proxyClass();
		vfsStreamWrapper::register();
		vfsStreamWrapper::setRoot(new vfsStreamDirectory('Foo'));
	}

	public function tearDown() {
		unset($this->proxy);
	}

	/**
	 * @test
	 */
	public function generateReturnsSqlStringIfRespectiveGeneratorExists() {
		$expected = 'SELECT * FROM foo;';

		$sqlGeneratorMock = $this->getMockBuilder('Tx_PtExtbase_SqlGenerator_PhpFileSqlGenerator')
				->setMethods(array('generate'))
				->getMock();
		$sqlGeneratorMock->expects($this->once())
			->method('generate')
			->will($this->returnValue($expected));

		$this->proxy->_set('sqlGenerators', array('php' => $sqlGeneratorMock));

		$filePath = 'vfs://Foo/Bar.php';
		file_put_contents($filePath, 'foobar');

		$actual = $this->proxy->generate($filePath);
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @test
	 * @expectedException Exception
	 * @expectedExceptionMessage Not a valid file extension:
	 */
	public function generateThrowsExceptionIfFileExtensionIsNotValid() {
		$sqlGeneratorMock = $this->getMockBuilder('Tx_PtExtbase_SqlGenerator_PhpFileSqlGenerator')
				->getMock();

		$this->proxy->_set('sqlGenerators', array('php' => $sqlGeneratorMock));

		$filePath = 'vfs://Foo/Bar.sql';
		file_put_contents($filePath, 'foobar');

		$this->proxy->generate($filePath);
	}

	/**
	 * @test
	 * @expectedException Exception
	 * @expectedExceptionMessage Not a valid file:
	 */
	public function checkFilePathThrowsExceptionIfFilePathDoesNotExist() {
		$filePath = 'vfs://Foo/Bar.sql';
		file_put_contents($filePath, 'foobar');
		$this->proxy->_call('checkFilePath', 'vfs://Foo/Baz.sql');
	}

}
?>