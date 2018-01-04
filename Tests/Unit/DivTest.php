<?php
namespace PunktDe\PtExtbase\Tests;

/***************************************************************
 *  Copyright (C) 2016 punkt.de GmbH
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

use PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase;

class DivTest extends AbstractBaseTestcase
{

    /**
     * @var \Tx_PtExtbase_Div
     */
    protected $proxy;

    public function setUp()
    {
        $proxyClass = $this->buildAccessibleProxy(\Tx_PtExtbase_Div::class);
        $this->proxy = new $proxyClass();
    }

    /**
     * building this with a data provider actually makes the test harder to read and to understand
     *
     * @test
     */
    public function returnExtConfArrayTest()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'] = [
            'validArray' => [
                'data' => 'value'
            ],
            'validSerialized' => 'a:1:{s:5:"data2";s:13:"value as well";}',
            'somethingInvalid' => 'a:2:{s:8:"data";s:1:"value";}'
        ];

        $this->assertEquals([
            'data' => 'value'
        ], $this->proxy->returnExtConfArray('validArray'));

        $this->assertEquals([
            'data2' => 'value as well'
        ], $this->proxy->returnExtConfArray('validSerialized'));

        try {
            $this->proxy->returnExtConfArray('invalidExtensionKey');
        } catch (\Exception $e) {
            $this->assertEquals(1473087212, $e->getCode());
        }
    }
}