<?php
/***************************************************************
 *  Copyright (C) 2014 punkt.de GmbH
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

use PunktDe\PtExtbase\Logger\LoggerConfiguration;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Neos\Utility\Files;


class Tx_PtExtbase_Tests_Functional_Logger_LoggerTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var string
     */
    protected $proxyClass;


    /**
     * @var string
     */
    protected $logFilePath;


    /**
     * @var \PunktDe\PtExtbase\Logger\Logger
     */
    protected $logger;


    /**
     * @var string
     */
    protected $logExceptionsPath;


    /**
     * @var \TYPO3\CMS\Extbase\Object\Container\Container
     */
    protected $objectContainer;


    /**
     * @return void
     */
    public function setUp()
    {
        $this->objectContainer = $this->objectManager->get(\TYPO3\CMS\Extbase\Object\Container\Container::class);

        $this->prepareWorkingDirectories();
        $this->prepareMailMessageMock();
        $this->prepareLoggerConfigurationMock();
        $this->prepareUserAgentMock();
        $this->prepareServerInformationMock();
        $this->prepareRequestInformationMock();
        $this->prepareLogger();
    }


    /**
     * @return void
     */
    protected function prepareWorkingDirectories()
    {
        $this->logFilePath =  Files::concatenatePaths([__DIR__, '/Logs/TestLog.log']);
        $this->logExceptionsPath = Files::concatenatePaths([__DIR__, '/Logs/Exceptions/']);
        Files::createDirectoryRecursively($this->logExceptionsPath);
    }



    /**
     * @return void
     */
    protected function prepareMailMessageMock()
    {
        $this->objectContainer->registerImplementation(\TYPO3\CMS\Core\Mail\MailMessage::class, 'Tx_PtTest_Mock_SwiftMessage');
    }



    /**
     * @return void
     */
    protected function prepareLoggerConfigurationMock()
    {
        $this->getMockBuilder(LoggerConfiguration::class)
            ->setMockClassName('Tx_PtExtbase_Logger_LoggerConfigurationMock')
            ->setMethods(['getLogLevelThreshold', 'getEmailLogLevelThreshold', 'weHaveAnyEmailReceivers', 'getEmailReceivers'])
            ->getMock();
        $loggerConfigurationMock = $this->objectManager->get('Tx_PtExtbase_Logger_LoggerConfigurationMock');
        $loggerConfigurationMock->expects($this->any())
            ->method('getLogLevelThreshold')
            ->will($this->returnValue(LogLevel::DEBUG));
        $loggerConfigurationMock->expects($this->any())
            ->method('getEmailLogLevelThreshold')
            ->will($this->returnValue(LogLevel::INFO));
        $loggerConfigurationMock->expects($this->any())
            ->method('weHaveAnyEmailReceivers')
            ->will($this->returnValue(true));
        $loggerConfigurationMock->expects($this->any())
            ->method('getEmailReceivers')
            ->will($this->returnValue('ry28@hugo10.intern.punkt.de'));

        $this->objectContainer->registerImplementation(LoggerConfiguration::class, 'Tx_PtExtbase_Logger_LoggerConfigurationMock');
    }



    /**
     * @return void
     */
    protected function prepareUserAgentMock()
    {
        $this->getMockBuilder('Tx_PtExtbase_Utility_UserAgent')
            ->setMethods(['getUserAgentData'])
            ->setMockClassName('Tx_PtExtbase_Utility_UserAgentMock')
            ->getMock();
        $userAgentMock = $this->objectManager->get('Tx_PtExtbase_Utility_UserAgentMock');
        $userAgentMock->expects($this->any())
            ->method('getUserAgentData')
            ->will($this->returnValue('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36'));

        $this->objectContainer->registerImplementation('Tx_PtExtbase_Utility_UserAgent', 'Tx_PtExtbase_Utility_UserAgentMock');
    }



    /**
     * @return void
     */
    protected function prepareServerInformationMock()
    {
        $this->getMockBuilder('Tx_PtExtbase_Utility_ServerInformation')
            ->setMethods(['getServerHostName'])
            ->setMockClassName('ServerInformationMock')
            ->getMock();
        $serverInformationMock = $this->objectManager->get('ServerInformationMock');
        $serverInformationMock->expects($this->any())
            ->method('getServerHostName')
            ->will($this->returnValue('spiderman.web.net'));

        $this->objectContainer->registerImplementation('Tx_PtExtbase_Utility_ServerInformation', 'ServerInformationMock');
    }



    /**
     * @return void
     */
    protected function prepareRequestInformationMock()
    {
        $this->getMockBuilder('PunktDe\PtExtbase\Utility\RequestInformation')
            ->setMethods(['getCurrentRequestId'])
            ->setMockClassName('RequestInformationMock')
            ->getMock();
        $requestInformationMock = $this->objectManager->get('RequestInformationMock');
        $requestInformationMock->expects($this->any())
            ->method('getCurrentRequestId')
            ->will($this->returnValue('CongratulationsThisIsRequestNumber1000000'));

        $this->objectContainer->registerImplementation('PunktDe\PtExtbase\Utility\RequestInformation', 'RequestInformationMock');
    }



    /**
     * @return void
     */
    protected function prepareLogger()
    {
        $this->proxyClass = $this->buildAccessibleProxy(\PunktDe\PtExtbase\Logger\Logger::class);
        $this->logger = $this->objectManager->get($this->proxyClass);
        $this->logger->configureLogger($this->logFilePath, $this->logExceptionsPath);
    }



    /**
     * @throws Exception
     * @return void
     */
    public function tearDown()
    {
        unset($this->logger);
        file_put_contents($this->logFilePath, ''); // Clear File
        \Neos\Utility\Files::emptyDirectoryRecursively($this->logExceptionsPath);
    }



    /**
     * @test
     * @group loggerTest
     */
    public function logInfoWithoutFurtherParameter()
    {
        $this->logger->info('test');
        $this->assertLogFileContains('component="PunktDe.PtExtbase.Logger.Logger": test');
        $this->assertLogFileContains('[INFO]');
    }



    /**
     * @test
     * @group loggerTest
     */
    public function logInfoWithClassName()
    {
        $this->logger->info('test', __CLASS__);
        $this->assertLogFileContains('component="Tx.PtExtbase.Tests.Functional.Logger.LoggerTest": test');
        $this->assertLogFileContains('[INFO]');
    }



    /**
     * @test
     * @group loggerTest
     */
    public function logInfoWithClassNameAndAdditionlData()
    {
        $this->logger->info('test', __CLASS__, ['BLA']);
        $this->assertLogFileContains(' component="Tx.PtExtbase.Tests.Functional.Logger.LoggerTest": test - ["BLA"]');
        $this->assertLogFileContains('[INFO]');
    }



    /**
     * @test
     * @group loggerTest
     */
    public function logException()
    {
        try {
            throw new \Exception('This is a Test Exception');
        } catch (\Exception $e) {
            $this->logger->logException($e);
        }

        $this->assertLogFileContains('[CRITICAL]');
        $this->assertLogFileContains('component="PunktDe.PtExtbase.Logger.Logger": Uncaught exception: This is a Test Exception - See also:');

        $this->assertCount(1, \Neos\Utility\Files::readDirectoryRecursively($this->logExceptionsPath));
    }



    /**
     * @param $expectedString
     */
    protected function assertLogFileContains($expectedString)
    {
        if (!file_exists($this->logFilePath)) {
            sleep(1);
        }

        $this->assertFileExists($this->logFilePath);
        $data = file_get_contents($this->logFilePath);
        $this->assertNotEmpty($data);
        $this->assertContains($expectedString, $data, sprintf('Expected "%s" - But Log File is "%s"', $expectedString, $data));
    }



    /**
     * @test
     * @group loggerTest
     */
    public function loggerSendsEmailOnError()
    {
        $mailerMock = $this->objectManager->get('Tx_PtTest_Utility_Mailer'); /** @var Tx_PtTest_Utility_Mailer $mailerMock */
        $mailerMock->prepare();

        $this->logger->critical('The fantastic three', get_class($this), ['time' => 42.1337, 'name' => 'Summer', 'part' => 'Sun', 'multiPart' => ['a', 'b', 'c'], 'weather' => 'Sunshine']);

        $mail = $mailerMock->getFirstMail();

        $this->assertEquals(file_get_contents(ExtensionManagementUtility::extPath('pt_extbase') . 'Tests/Functional/Logger/ExpectedErrorMail.txt'), $mail->getBody());
    }
}
