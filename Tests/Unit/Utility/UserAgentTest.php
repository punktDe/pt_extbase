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
 * Test case for Tx_PtExtbase_Utility_UserAgent
 *
 * @package pt_extbase
 * @subpackage Tests\Unit\Utility
 */
class Tx_PtExtbase_Tests_Unit_Utility_UserAgentTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var string
     */
    protected $proxyClass;


    /**
     * @var Tx_PtExtbase_Utility_UserAgent
     */
    protected $proxyMock;


    public function setUp()
    {
        $this->proxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Utility_UserAgent');
        $this->proxyMock = $this->getMockBuilder($this->proxyClass)
                ->setMethods(array('getUserAgentData'))
                ->getMock();
    }



    public function tearDown()
    {
        unset($this->proxyMock);
    }



    /**
     * @return array
     */
    public function getOperationSystemReturnsExpectedOperatingSystemDataProvider()
    {
        return array(
            'MacOS' => array(
                'agentData' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36',
                'expected' => 'Mac OS X'
            ),
            'UnknownSystem' => array(
                'agentData' => 'Mozilla/5.0 (HyperFastBetterThanAnyOtherSystem) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36',
                'expected' => 'No known operating system found in HTTP_USER_AGENT: Mozilla/5.0 (HyperFastBetterThanAnyOtherSystem) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36'
            )
        );
    }



    /**
     * @param string $agentData
     * @param string $expected
     *
     * @test
     * @dataProvider getOperationSystemReturnsExpectedOperatingSystemDataProvider
     */
    public function getOperationSystemReturnsExpectedOperatingSystem($agentData, $expected)
    {
        $this->proxyMock->expects($this->any())
                ->method('getUserAgentData')
                ->will($this->returnValue($agentData));
        $this->assertSame($expected, $this->proxyMock->getOperatingSystem());
    }



    /**
     * @return array
     */
    public function getBrowserReturnsExpectedBrowserDataProvider()
    {
        return array(
            'Chrome' => array(
                'agentData' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36',
                'expected' => 'Chrome'
            ),
            'UnknownSystem' => array(
                'agentData' => 'Mozilla/5.0 (HyperFastBetterThanAnyOtherSystem) AppleWebKit/537.36 (KHTML, like Gecko) SuperBrowser/35.0.1916.153 NoBrowser/537.36',
                'expected' => 'No known browser found in HTTP_USER_AGENT: Mozilla/5.0 (HyperFastBetterThanAnyOtherSystem) AppleWebKit/537.36 (KHTML, like Gecko) SuperBrowser/35.0.1916.153 NoBrowser/537.36'
            )
        );
    }



    /**
     * @param string $agentData
     * @param string $expected
     *
     * @test
     * @dataProvider getBrowserReturnsExpectedBrowserDataProvider
     */
    public function getBrowserReturnsExpectedBrowser($agentData, $expected)
    {
        $this->proxyMock->expects($this->any())
                ->method('getUserAgentData')
                ->will($this->returnValue($agentData));
        $this->assertSame($expected, $this->proxyMock->getBrowser());
    }
}
