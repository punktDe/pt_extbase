<?php
namespace PunktDe\PtExtbase\Tests\Functional\Scheduler;

/*
 * This file is part of the PunktDe\PtExtbase package.
 *
 * This package is open source software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use PunktDe\PtExtbase\Scheduler\AbstractSchedulerTask;
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
     * @var FlashMessageQueue
     */
    protected $flashMessageQueue;


    /**
     * @return boolean
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $flashMessage = GeneralUtility::makeInstance(
                    FlashMessage::class,
                    'This Task is created for testing purposes, it creates some test files and log entries in the application log',
                    '',
                    FlashMessage::WARNING,
                    true
                ); /** @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
            $this->flashMessageQueue->addMessage($flashMessage);
            $executeTestFilePath = Files::concatenatePaths([$this->testPath, 'testTaskExecution.txt']);
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
        $this->flashMessageQueue = $this->objectManager->get(FlashMessageQueue::class, 'TestTask');

        $this->testPath = Files::concatenatePaths([__DIR__, '/WorkingDirectory']);

        $testInitializeObjectFilePath = Files::concatenatePaths([$this->testPath, 'testTaskObjectInitialization.txt']);
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
