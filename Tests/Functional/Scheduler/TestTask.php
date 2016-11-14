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


use PunktDe\PtExtbase\Scheduler\AbstractSchedulerTask;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Messaging\FlashMessage;
use \TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use PunktDe\PtExtbase\Utility\Files;

class TestTask extends AbstractSchedulerTask
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;


    /**
     * @var string
     */
    protected $testPath = '';


    /**
     * @return boolean
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $flashMessage = GeneralUtility::makeInstance(
                    't3lib_FlashMessage',
                    'This Task is created for testing purposes, it creates some test files and log entries in the application log',
                    '',
                    FlashMessage::WARNING,
                    true
                );
            FlashMessageQueue::addMessage($flashMessage);
            $executeTestFilePath = Files::concatenatePaths(array($this->testPath, 'testTaskExecution.txt'));
            file_put_contents($executeTestFilePath, '1428924570');

            return true;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('%s (%s)', $e->getMessage(), $e->getCode()));
            throw $e;
        }
    }



    /**
     * @return void
     */
    public function initializeObject()
    {
        $this->testPath = Files::concatenatePaths(array(__DIR__, '/WorkingDirectory'));

        $testInitializeObjectFilePath = Files::concatenatePaths(array($this->testPath, 'testTaskObjectInitialization.txt'));
        file_put_contents($testInitializeObjectFilePath, '1428924552');
    }


    /**
     * @param $loggerData
     */
    public function enrichTaskLoggerData(&$loggerData)
    {
        $loggerData['additionalTestLogEntry'] = '1429106236';
    }


    /**
     * Return the extensionName for Extbase Initialization
     *
     * @return string
     */
    public function getExtensionName()
    {
        return 'PtExtbase';
    }
}
