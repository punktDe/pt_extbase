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

use PunktDe\PtExtbase\Utility\Git\Command\StatusCommand;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Status Command Test Case
 *
 * @package PunktDe\PtExtbase\Tests\Utility\Git\Command
 */
class StatusCommandTest extends UnitTestCase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Git\Command\StatusCommand
     */
    protected $statusCommand;


    /**
     * @return void
     */
    public function setUp()
    {
        $this->statusCommand = new StatusCommand();
    }



    /**
     * @test
     */
    public function checkIfStatusCommandIsExtractedFromClassName()
    {
        $expected = "status";
        $actual = $this->statusCommand->getCommandName();
        $this->assertSame($expected, $actual);
    }



    /**
     * @test
     */
    public function getResultClassNameReturnsValidClassName()
    {
        $expected = 'PunktDe\PtExtbase\Utility\Git\Result\StatusResult';
        $actual = $this->statusCommand->getResultType();
        $this->assertSame($expected, $actual);
    }



    /**
     * @test
     */
    public function getResultClassNameReturnsBaseResultClassIfNoDedicatedResultClassExists()
    {
        $commandMock = $this->getMockBuilder('PunktDe\PtExtbase\Utility\Git\Command\StatusCommand')
            ->setMethods(['getClass'])
            ->getMock();
        $commandMock->expects($this->any())
            ->method('getClass')
            ->will($this->returnValue('PunktDe\PtExtbase\Utility\Git\Command\FooCommand'));

        $expected = 'PunktDe\PtExtbase\Utility\Git\Result\Result';
        $actual = $commandMock->getResultType();
        $this->assertSame($expected, $actual);
    }
}
