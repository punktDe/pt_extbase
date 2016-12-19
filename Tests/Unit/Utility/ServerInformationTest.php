<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 punkt.de GmbH
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
 * ServerInformation Testcase
 *
 * @package pt_extbase
 * @subpackage Tests\Unit\Utility
 */
class Tx_PtExtbase_Utility_ServerInformationTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    protected $proxyClass;

    /**
     * @var Tx_PtExtbase_Utility_ServerInformation
     */
    protected $proxy;

    public function setUp()
    {
        $this->proxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Utility_ServerInformation');
        $this->proxy = new $this->proxyClass();
    }

    public function tearDown()
    {
        unset($this->proxy);
    }

    /**
     * @return array
     */
    public function getServerHostNameReturnsExpectedValuesDataProvider()
    {
        return [
            [
                'server' => [
                    'HOSTNAME' => 'Jupiter',
                    'HTTP_HOST' => 'Saturn'
                ],
                'expected' => 'Jupiter'
            ],
            [
                'server' => [
                    'HTTP_HOST' => 'Saturn'
                ],
                'expected' => 'Saturn'
            ],
            [
                'server' => [
                    'HOSTNAME' => 'Jupiter'
                ],
                'expected' => 'Jupiter'
            ],
            [
                'server' => [
                ],
                'expected' => ''
            ]
        ];
    }

    /**
     * @param array $server
     * @param string $expected
     *
     * @test
     * @dataProvider getServerHostNameReturnsExpectedValuesDataProvider
     */
    public function getServerHostNameReturnsExpectedValues($server, $expected)
    {
        $_SERVER = $server;
        $actual = $this->proxy->getServerHostName();
        $this->assertSame($expected, $actual);
    }
}
