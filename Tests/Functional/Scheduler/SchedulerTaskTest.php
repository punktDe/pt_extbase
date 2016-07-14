<?php

namespace PunktDe\PtExtbase\Tests\Functional\Scheduler;

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


use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\Flow\Utility\Files;

/**
 * Test case for class PunktDe\PtExtbase\Scheduler\AbstractSchedulerTask
 *
 * @package pt_extbase
 */
class SchedulerTaskTest extends \PunktDe\PtExtbase\Testing\Unit\AbstractBaseTestcase
{
    /**
     * @var string
     */
    protected $testFilePath = '';


    /**
     * @var string
     */
    protected $logFilePath = '';


    /**
     * @var string
     */
    protected $schedulerTaskId = '';

    /**
     * @var \Tx_PtExtbase_Logger_LoggerConfiguration
     */
    protected $loggerConfiguration;


    protected function setUp()
    {
        $this->prepareTestPaths();
        Files::createDirectoryRecursively($this->testFilePath);
        touch($this->logFilePath);

        $this->schedulerTaskId = $this->getTestTaskId();
    }


    /**
     * @test
     */
    public function schedulerTask()
    {
        shell_exec(PATH_typo3.'cli_dispatch.phpsh scheduler -f -i '. $this->schedulerTaskId);

        $this->objectInitializationSuccessful();

        $this->taskExecuteSuccessful();

        $this->loggingSuccessful();
    }


    protected function prepareTestPaths()
    {
        $this->testFilePath = \PunktDe\PtExtbase\Utility\Files::concatenatePaths(array(__DIR__, 'WorkingDirectory'));

        $this->loggerConfiguration = $this->objectManager->get('Tx_PtExtbase_Logger_LoggerConfiguration');
        $this->logFilePath = $this->loggerConfiguration->getLogFilePath();
    }


    /**
     * @return mixed
     */
    protected function getTestTaskId()
    {
        $typo3db = $GLOBALS['TYPO3_DB']; /** @var $typo3db \TYPO3\CMS\Core\Database\DatabaseConnection */
        $res = $typo3db->exec_SELECTquery('uid', 'tx_scheduler_task', 'serialized_task_object LIKE "%TestTask%"', '', 'uid', '1');
        $testTaskRow = $typo3db->sql_fetch_assoc($res);
        $typo3db->sql_free_result($res);
        return $testTaskRow['uid'];
    }


    /**
     * @return string
     */
    protected function getFileContent($filePath)
    {
        $file = fopen($filePath, 'r');
        $fileContent = fread($file, filesize($filePath));
        return $fileContent;
    }



    protected function objectInitializationSuccessful()
    {
        $initializeObjectTestFilePath = \PunktDe\PtExtbase\Utility\Files::concatenatePaths(array($this->testFilePath, "testTaskObjectInitialization.txt"));
        $initializeObjectTestFileContent = $this->getFileContent($initializeObjectTestFilePath);

        $this->assertEquals('1428924552', $initializeObjectTestFileContent, 'The content of the "initializeTestFile" file is not as expected');
    }



    protected function taskExecuteSuccessful()
    {
        $executeTestFilePath = \PunktDe\PtExtbase\Utility\Files::concatenatePaths(array($this->testFilePath, "testTaskExecution.txt"));
        $executeTestFileContent = $this->getFileContent($executeTestFilePath);

        $this->assertEquals('1428924570', $executeTestFileContent, 'The content of the "executeTestFile" file is not as expected');
    }



    protected function loggingSuccessful()
    {
        $logTestFileContent = $executeTestFileContent = $this->getFileContent($this->logFilePath);
        $logTestFileContentArray = array();
        preg_match('|^(?<Timestamp>.*?[\+-]\d\d\d\d) \[(?<LogLevel>[^\]]+)\] request="(?<RequestId>[^"]+)" component="(?<Component>[^"]+)": (?<Message>[^{}]*)(?<Data>.*)?|', $logTestFileContent, $logTestFileContentArray);
        $this->assertEquals('PunktDe.PtExtbase.Tests.Functional.Scheduler.TestTask', $logTestFileContentArray['Component'], 'The content of the file is not as expected');

        $logData = json_decode($logTestFileContentArray['Data'], true);

        $this->assertTrue(array_key_exists('time', $logData), 'The task runtime is not written to the log');

        $this->assertEquals('1429106236', $logData['additionalTestLogEntry'], 'The additional log Data provided by the Scheduler Task is not parsed');
    }


    protected function tearDown()
    {
        Files::removeDirectoryRecursively($this->testFilePath);
        unlink($this->logFilePath);
    }
}
