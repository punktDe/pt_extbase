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
 * Test case for class Tx_PtExtbase_Parser_Sql_MultipleQueriesFileParser
 *
 * @package pt_extbase
 * @subpackage Tests\Unit\Parser\Sql
 */
class Tx_PtExtbase_Tests_Unit_Parser_SqlMultipleQueriesFileParserTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	protected $proxyClass;

	protected $proxy;

	public function setUp() {
		$this->proxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Parser_Sql_MultipleQueriesFileParser');
		$this->proxy = new $this->proxyClass();
	}

	public function tearDown() {
		unset($this->proxy);
	}

	/**
	 * @test
	 */
	public function parseReturnsValidArrayOfQueries() {
		$input = 'tables.sql';

		$sql = array(
			"-- CREATE TABLE",
			"CREATE TABLE IF NOT EXISTS tx_ptextbase_domain_model_category (",
	   			"uid int(11) NOT NULL AUTO_INCREMENT,",
	   			"title varchar(255) DEFAULT '' NOT NULL,",
	   			"PRIMARY KEY (uid),",
	   			"KEY(category_title)",
			") ENGINE=MyISAM DEFAULT CHARACTER SET utf8;",
			"-- DROP TABLE",
			"DROP TABLE IF EXISTS tx_ptextbase_domain_model_category;"
		);

		$expected = array(
			"CREATE TABLE IF NOT EXISTS tx_ptextbase_domain_model_category (uid int(11) NOT NULL AUTO_INCREMENT,title varchar(255) DEFAULT '' NOT NULL,PRIMARY KEY (uid),KEY(category_title)) ENGINE=MyISAM DEFAULT CHARACTER SET utf8;",
			"DROP TABLE IF EXISTS tx_ptextbase_domain_model_category;"
		);

		$proxyMock = $this->getMockBuilder($this->proxyClass)
				->setMethods(array('loadSqlFile'))
				->getMock();
		$proxyMock->expects($this->once())
			->method('loadSqlFile')
			->with($input)
			->will($this->returnValue($sql));

		$actual = $proxyMock->parse($input);
		$this->assertEquals($expected, $actual);
	}

}
?>