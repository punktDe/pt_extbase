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
 * Test case for class Tx_PtExtbase_SqlGenerator_PhpFileSqlGenerator
 *
 * @package pt_extbase
 * @subpackage Tests\Unit\Parser\Sql
 */
class Tx_PtExtbase_Tests_Unit_SqlGenerator_PhpFileSqlGeneratorTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	protected $proxyClass;

	protected $proxy;

	public function setUp() {
		$this->proxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_SqlGenerator_PhpFileSqlGenerator');
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
	public function getClassNamesReturnsClassNamesOfPhpFileAs() {
		$fileContents = "
		<?php\n
		class Tx_PtWhatEver_Domain_Sql_SqlGenerator implements Tx_PtExtbase_SqlGenerator_SqlGeneratorCommandInterface {\n
			public function generate() {\n
				return array('SELECT * FROM foobar;');\n
			}\n
		}\n
		class Tx_PtWhatEver_Domain_Sql_SuperSqlGenerator implements Tx_PtExtbase_SqlGenerator_SqlGeneratorCommandInterface {\n
			public function generate() {\n
				return array('SELECT * FROM barbaz;');\n
			}\n
		}\n
		?>";
		file_put_contents("vfs://Foo/Bar.php", $fileContents);

		$expected = array(
			'Tx_PtWhatEver_Domain_Sql_SqlGenerator',
			'Tx_PtWhatEver_Domain_Sql_SuperSqlGenerator'
		);

		$this->proxy->_call('getClassNames', 'vfs://Foo/Bar.php');
		$actual = $this->proxy->_get('classNames');
		$this->assertEquals($expected, $actual);
	}

	/**
	 * @test
	 */
	public function generateSqls() {
		$sql1 = array(
			'SELECT * FROM foo;',
			'SELECT * FROM bar;',
		);
		$sql2 = array(
			'SELECT * FROM bar;',
			'SELECT * FROM baz;',
		);

		$expected = array(
			'SELECT * FROM foo;',
			'SELECT * FROM bar;',
			'SELECT * FROM bar;',
			'SELECT * FROM baz;'
		);

		$sqlGenerator1 = $this->getMockBuilder('Tx_PtExtbase_SqlGenerator_PhpFileSqlGenerator')
				->setMethods(array('generate'))
				->getMock();
		$sqlGenerator1->expects($this->once())
			->method('generate')
			->will($this->returnValue($sql1));
		$sqlGenerator2 = $this->getMockBuilder('Tx_PtExtbase_SqlGenerator_PhpFileSqlGenerator')
				->setMethods(array('generate'))
				->getMock();
		$sqlGenerator2->expects($this->once())
				->method('generate')
				->will($this->returnValue($sql2));

		$classNames = array(
			'Tx_PtExtbase_SqlGenerator_PhpFileGenerator' => $sqlGenerator1,
			'Tx_PtExtbase_SqlGenerator_SuperPhpFileGenerator' => $sqlGenerator2
		);

		$objectManagerMock = $this->getMockBuilder('Tx_Extbase_Object_ObjectManager')
				->setMethods(array('get'))
				->getMock();
		$objectManagerMock->expects($this->exactly(2))
				->method('get')
			->will($this->returnCallback(
			function ($className) use ($classNames) {
				return $classNames[$className];
			}));

		$this->proxy->_set('classNames', array_keys($classNames));
		$this->proxy->_set('objectManager', $objectManagerMock);
		$actual = $this->proxy->_call('generateSqls');
		$this->assertEquals($expected, $actual);
	}

}
?>