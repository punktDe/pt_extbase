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
class Tx_PtExtbase_Logger_LoggerTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_PtExtbase_Logger_Logger
	 */
	protected $logger;

	/**
	 * @var string
	 */
	protected $logFilePath;

	/**
	 * @var string
	 */
	protected $logExceptionsPath;


	public function setUp() {


		if (!class_exists('\TYPO3\CMS\Core\Log\Logger') && !class_exists('t3lib_log_Logger')) {
			$this->markTestSkipped('you must use either TYPO3 6.X or the extension "t3lib_log"');
		}

		$this->logFilePath = __DIR__ . '/Logs/TestLog.log';
		$this->logExceptionsPath = __DIR__ . '/Logs/Exceptions/';

		$this->logger = $this->objectManager->get('Tx_PtExtbase_Logger_Logger');
		$this->logger->configureLogger($this->logFilePath, $this->logExceptionsPath);

	}


	public function tearDown() {
		unset($this->logger);
		file_put_contents($this->logFilePath, ''); // Clear File
		Tx_PtExtbase_Utility_Files::emptyDirectoryRecursively($this->logExceptionsPath);
	}


	/**
	 * @test
	 */
	public function logInfoWithoutFurtherParameter(){
		$this->logger->info('test');
		$this->assertLogFileContains('component="Tx.PtExtbase.Logger.Logger": test');
		$this->assertLogFileContains('[INFO]');
	}

	/**
	 * @test
	 */
	public function logInfoWithClassName(){
		$this->logger->info('test', __CLASS__);
		$this->assertLogFileContains('component="Tx.PtExtbase.Logger.LoggerTest": test');
		$this->assertLogFileContains('[INFO]');
	}

	/**
	 * @test
	 */
	public function logInfoWithClassNameAndAdditionlData(){
		$this->logger->info('test', __CLASS__, array('BLA'));
		$this->assertLogFileContains(' component="Tx.PtExtbase.Logger.LoggerTest": test - ["BLA"]');
		$this->assertLogFileContains('[INFO]');
	}


	/**
	 * @test
	 */
	public function logException() {

		try {
			throw new \Exception('This is a Test Exception');
		} catch(\Exception $e) {
			$this->logger->logException($e);
		}

		$this->assertLogFileContains('[CRITICAL]');
		$this->assertLogFileContains('component="Tx.PtExtbase.Logger.Logger": Uncaught exception: This is a Test Exception - See also:');

		$this->assertCount(1, Tx_PtExtbase_Utility_Files::readDirectoryRecursively($this->logExceptionsPath));
	}


	/**
	 * @param $expectedString
	 */
	protected function assertLogFileContains($expectedString) {
		if(!file_exists($this->logFilePath)) sleep(1);

		$this->assertFileExists($this->logFilePath);
		$data = file_get_contents($this->logFilePath);
		$this->assertNotEmpty($data);
		$this->assertContains($expectedString, $data, sprintf('Expected "%s" - But Log File is "%s"', $expectedString, $data));
	}


}

