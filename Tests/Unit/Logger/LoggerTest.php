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

/**
 * Logger Testcase
 *
 * @subpackage Tests\Unit\Service
 */
class Tx_PtExtbase_Tests_Unit_Logger_LoggerTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {

	/**
	 * @var string
	 */
	protected $proxyClass;


	/**
	 * @var Tx_PtExtbase_Logger_Logger
	 */
	protected $proxy;


	/**
	 * @return void
	 */
	public function setUp() {
		$this->proxyClass = $this->buildAccessibleProxy('Tx_PtExtbase_Logger_Logger');
		$this->proxy = new $this->proxyClass();
	}



	/**
	 * @test
	 */
	public function configureLoggerPropertiesSetsValidConfiguration() {
		$expectedLogPath = '/var/apache/partnerportal/log/EsalesLog';
		$expectedLogLevelThreshold = \TYPO3\CMS\Core\Log\LogLevel::INFO;
		$expectedEmailLogLevelThreshold = \TYPO3\CMS\Core\Log\LogLevel::CRITICAL;
		$expectedEmailReceivers = 'bud@spencer.it,terence@hill.de';

		$loggerConfigurationMock = $this->getMockBuilder('Tx_PtExtbase_Logger_LoggerConfiguration')
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
			->will($this->returnValue(TRUE));
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
		$this->assertArrayHasKey(\TYPO3\CMS\Core\Log\LogLevel::DEBUG, $GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration']);
		$this->assertArrayHasKey('PunktDe\\PtExtbase\\Logger\\Processor\\ReplaceComponentProcessor', $GLOBALS['TYPO3_CONF_VARS']['LOG']['processorConfiguration'][\TYPO3\CMS\Core\Log\LogLevel::DEBUG]);

	}




}
