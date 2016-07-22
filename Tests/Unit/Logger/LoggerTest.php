<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011-2012 punkt.de GmbH <extensions@punkt.de>
 *  Authors: Ursula Klinger, Joachim Mathes
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

use PunktDe\PtExtbase\Logger\LoggerConfiguration;
use PunktDe\PtExtbase\Logger\LoggerManager;
use PunktDe\PtExtbase\Logger\Processor\ReplaceComponentProcessor;
use TYPO3\CMS\Core\Log\LogLevel;

/**
 * Logger Testcase
 *
 * @subpackage Tests\Unit\Service
 */
class Tx_PtExtbase_Tests_Unit_Logger_LoggerTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var string
     */
    protected $proxyClass;


    /**
     * @var \PunktDe\PtExtbase\Logger\Logger
     */
    protected $proxy;


    /**
     * @return void
     */
    public function setUp()
    {
        $this->proxyClass = $this->buildAccessibleProxy(\PunktDe\PtExtbase\Logger\Logger::class);
        $this->proxy = $this->objectManager->get($this->proxyClass);
    }



    /**
     * @test
     */
    public function configureLoggerPropertiesSetsValidConfiguration()
    {
        $expectedLogPath = '/var/apache/partnerportal/log/EsalesLog';
        $expectedLogLevelThreshold = LogLevel::INFO;
        $expectedEmailLogLevelThreshold = LogLevel::CRITICAL;
        $expectedEmailReceivers = 'bud@spencer.it,terence@hill.de';

        $loggerConfigurationMock = $this->getMockBuilder(LoggerConfiguration::class)
            ->setMethods(array('getLogLevelThreshold', 'getEmailLogLevelThreshold', 'weHaveAnyEmailReceivers', 'getEmailReceivers'))
            ->getMock();
        $loggerConfigurationMock->expects($this->once())
            ->method('getLogLevelThreshold')
            ->will($this->returnValue($expectedLogLevelThreshold));
        $loggerConfigurationMock->expects($this->once())
            ->method('getEmailLogLevelThreshold')
            ->will($this->returnValue($expectedEmailLogLevelThreshold));
        $loggerConfigurationMock->expects($this->once())
            ->method('weHaveAnyEmailReceivers')
            ->will($this->returnValue(true));
        $loggerConfigurationMock->expects($this->once())
            ->method('getEmailReceivers')
            ->will($this->returnValue($expectedEmailReceivers));

        $this->proxy->_set('logFilePath', $expectedLogPath);
        $this->proxy->_set('loggerConfiguration', $loggerConfigurationMock);
        $this->proxy->_call('configureLoggerProperties');

        $this->assertArrayHasKey($expectedLogLevelThreshold, $GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration']);
        $this->assertSame($expectedLogPath, $GLOBALS['TYPO3_CONF_VARS']['LOG']['writerConfiguration'][$expectedLogLevelThreshold]['Tx_PtExtbase_Logger_Writer_FileWriter']['logFile']);
        $this->assertArrayHasKey($expectedEmailLogLevelThreshold, $GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration']);
        $this->assertSame(array('receivers' => $expectedEmailReceivers), $GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration'][$expectedEmailLogLevelThreshold]['Tx_PtExtbase_Logger_Processor_EmailProcessor']);
        $this->assertArrayHasKey(LogLevel::DEBUG, $GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration']);
        $this->assertArrayHasKey(ReplaceComponentProcessor::class, $GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration'][LogLevel::DEBUG]);
    }

    /**
     * @test
     */
    public function enrichLogDataByComponentCallsLoggerSpecificMethod()
    {
        $loggerMock = $this->getMockBuilder(\PunktDe\PtExtbase\Logger\Logger::class)
            ->setMethods(array('enrichLoggerSpecificDataByComponent'))
            ->getMock();
        $loggerMock->expects($this->once())
            ->method('enrichLoggerSpecificDataByComponent');
        /** @var $loggerMock \PunktDe\PtExtbase\Logger\Logger */
        
        $loggerManager = $this->objectManager->get(LoggerManager::class);
        $loggerMock->injectLoggerManager($loggerManager);

        $data = array();
        $loggerMock->enrichLogDataByComponent($data, 'Extbase');
    }

    /**
     * @return array
     */
    public function enrichLogDataByComponentEnrichesDataArrayDataProvider()
    {
        return array(
            'noUserNoComponent' => array(
                'userId' => null,
                'component' => '',
                'expected' => array('loggerComponent' => 'PunktDe.PtExtbase.Logger.Logger')
            ),
            'givenUserNoComponent' => array(
                'userId' => 86,
                'component' => '',
                'expected' => array('UserID' => 86, 'loggerComponent' => 'PunktDe.PtExtbase.Logger.Logger')
            ),
            'noUserGivenComponent' => array(
                'userId' => null,
                'component' => 'Tx_PtMock_Domain_Model_Stuff',
                'expected' => array('loggerComponent' => 'Tx.PtMock.Domain.Model.Stuff')
            ),
            'givenUserAndComponent' => array(
                'userId' => 86,
                'component' => 'Tx_PtMock_Domain_Model_Stuff',
                'expected' => array('UserID' => 86, 'loggerComponent' => 'Tx.PtMock.Domain.Model.Stuff')
            ),
        );
    }

    /**
     * @test
     * @dataProvider enrichLogDataByComponentEnrichesDataArrayDataProvider
     *
     * @param integer $userId
     * @param string $component
     * @param array $expected
     */
    public function enrichLogDataByComponentEnrichesDataArray($userId, $component, $expected)
    {
        $actual = array();

        $GLOBALS['TSFE']->fe_user->user['uid'] = $userId;
        $this->proxy->enrichLogDataByComponent($actual, $component);

        $this->assertEquals($expected, $actual);
    }
}
