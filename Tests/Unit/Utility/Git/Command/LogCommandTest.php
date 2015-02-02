<?php
namespace PunktDe\PtExtbase\Tests\Utility\Git\Command;

/***************************************************************
 *  Copyright (C) 2015 punkt.de GmbH
 *  Authors: el_equipo <opiuqe_le@punkt.de>
 *
 *  This script is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published
 *  by the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Lesser General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use PunktDe\PtExtbase\Utility\Git\Command\LogCommand;
use \TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Git Command Test Case
 *
 * @package PunktDe\PtExtbase\Tests\Utility\Git\Command
 */
class LogCommandTest extends UnitTestCase {

	/**
	 * @var \PunktDe\PtExtbase\Utility\Git\Command\LogCommand
	 */
	protected $logCommand;


	/**
	 * @return void
	 */
	public function setUp() {
		$this->logCommand = new LogCommand();
	}



	/**
	 * @test
	 */
	public function checkIfLogCommandIsExtractedFromClassName() {
		$expected = "log";
		$actual = $this->logCommand->getCommandName();
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function getResultClassNameReturnsValidClassName() {
		$expected = 'PunktDe\PtExtbase\Utility\Git\Result\LogResult';
		$actual = $this->logCommand->getResultClassName();
		$this->assertSame($expected, $actual);
	}



	/**
	 * @test
	 */
	public function getResultClassNameReturnsBaseResultClassIfNoDedicatedResultClassExists() {
		$commandMock = $this->getMockBuilder('PunktDe\PtExtbase\Utility\Git\Command\LogCommand')
			->setMethods(array('getClass'))
			->getMock();
		$commandMock->expects($this->any())
			->method('getClass')
			->will($this->returnValue('PunktDe\PtExtbase\Utility\Git\Command\FooCommand'));

		$expected = 'PunktDe\PtExtbase\Utility\Git\Result\Result';
		$actual = $commandMock->getResultClassName();
		$this->assertSame($expected, $actual);
	}

}