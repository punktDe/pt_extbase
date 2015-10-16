<?php
namespace PunktDe\PtExtbase\Tests\Unit\Utility\Varnish;

/***************************************************************
 *  Copyright (C)  punkt.de GmbH
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
 * Varnish Administration Test Case
 *
 * @package pt_extbase
 * @subpackage PunktDe\PtExtbase\Tests\Unit\Utility\Git
 */
class VarnishAdministrationTest extends UnitTestCase
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Varnish\VarnishAdministration
     */
    protected $proxy;


    /**
     * @var string
     */
    protected $pathToGitCommand = '';


    /**
     * @var \TYPO3\CMS\Extbase\Object\Container\Container
     */
    protected $objectContainer;


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $shellCommandServiceMock;

    
    /**
     * @return void
     */
    public function setUp()
    {
        $this->prepareProxy();
    }



    /**
     * @return void
     */
    protected function prepareProxy()
    {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

        $this->objectContainer = $objectManager->get('TYPO3\CMS\Extbase\Object\Container\Container'); /** @var \TYPO3\CMS\Extbase\Object\Container\Container $objectContainer */

        $this->getMockBuilder('\Tx_PtExtbase_Logger_Logger')
            ->setMockClassName('LoggerMock')
            ->getMock();
        $objectManager->get('LoggerMock'); /** @var  $loggerMock \PHPUnit_Framework_MockObject_MockObject */
        $this->objectContainer->registerImplementation('\Tx_PtExtbase_Logger_Logger', 'LoggerMock');

        $this->getMockBuilder('PunktDe\PtExtbase\Utility\ShellCommandService')
            ->setMethods(array('execute'))
            ->setMockClassName('ShellCommandServiceMock')
            ->getMock();
        $this->shellCommandServiceMock = $objectManager->get('ShellCommandServiceMock'); /** @var  $shellCommandServiceMock \PHPUnit_Framework_MockObject_MockObject */
        $this->objectContainer->registerImplementation('PunktDe\PtExtbase\Utility\ShellCommandService', 'ShellCommandServiceMock');

        $proxyClass = $this->buildAccessibleProxy('PunktDe\PtExtbase\Utility\Varnish\VarnishAdministration');

        $this->proxy = $objectManager->get($proxyClass, '/usr/bin/varnishadm');
    }



    /**
     * @test
     */
    public function validCommandIsRendered()
    {
        $this->prepareShellCommandExpectations();

        $this->proxy->setSecretFile('/home/spencer/varnish-secret')
            ->setAddressAndPort('127.0.0.1:6082')
            ->banUrl()
            ->setUrl('spencer\.it/films.*')
            ->execute();
    }



    /**
     * @return void
     */
    protected function prepareShellCommandExpectations()
    {
        $this->shellCommandServiceMock->expects($this->any())
            ->method('execute')
            ->withConsecutive(
                array($this->equalTo('/usr/bin/varnishadm -S /home/spencer/varnish-secret -T 127.0.0.1:6082 "ban.url spencer\.it/films.*"'))
            );
    }
}
