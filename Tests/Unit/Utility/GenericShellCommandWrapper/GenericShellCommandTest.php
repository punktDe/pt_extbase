<?php
namespace PunktDe\PtExtbase\Tests\Utility\GenericShellCommand;

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

use \TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Generic Shell Command Test Case
 *
 * @package PunktDe\PtExtbase\Tests\Utility\Git\Command
 */
class GenericShellCommandTest extends UnitTestCase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\GenericShellCommand
     */
    protected $proxy;


    /**
     * @return void
     */
    public function setUp()
    {
        $this->proxy = $this->getAccessibleMockForAbstractClass('PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\GenericShellCommand');
    }


    /**
     * @test
     */
    public function checkIfBuildCommandRendersValidCommandString()
    {
        $expected = array(
            '--do this',
            '--count toThree',
            '--stop'
        );

        $this->proxy->_set('command', 'you');
        $this->proxy->_set('argumentMap', array(
            'do' => '--do %s',
            'count' => '--count %s',
            'stop' => '--stop'
        ));
        $this->proxy->_set('arguments', array(
            'do' => 'this',
            'count' => 'toThree',
            'stop' => true
        ));

        $actual = $this->proxy->_call('buildArguments');

        $this->assertEquals($expected, $actual);
    }
}
