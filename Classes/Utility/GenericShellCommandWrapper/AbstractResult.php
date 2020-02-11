<?php
namespace PunktDe\PtExtbase\Utility\GenericShellCommandWrapper;

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

use PunktDe\PtExtbase\Logger\Logger;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Abstract Result
 *
 * @package PunktDe\PtExtbase\Utility\Git\Result
 */
abstract class AbstractResult
{

    /**
     * @var GenericShellCommand
     */
    protected $command;


    /**
     * @var int
     */
    protected $exitCode;


    /**
     * @var string
     */
    protected $rawResult = '';


    /**
     * @var ResultObjectStorage
     */
    protected $result;


    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var ExecutionManager
     */
    protected $executionManager;


    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param Logger $logger
     */
    public function injectLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param ExecutionManager $executionManager
     */
    public function injectExecutionManager(ExecutionManager $executionManager): void
    {
        $this->executionManager = $executionManager;
    }


    /**
     * @param GenericShellCommand $command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }


    /**
     * @return void
     */
    public function initializeObject()
    {
        $this->result = $this->objectManager->get('PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\ResultObjectStorage');
        list($this->rawResult, $this->exitCode) = $this->executionManager->execute($this->command);
    }


    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }



    /**
     * @param int $exitCode
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;
    }



    /**
     * @return string
     */
    public function getRawResult()
    {
        return $this->rawResult;
    }



    /**
     * @param string $rawResult
     */
    public function setRawResult($rawResult)
    {
        $this->rawResult = $rawResult;
    }



    /**
     * @return ObjectStorage
     */
    public function getResult()
    {
        if (count($this->result) === 0) {
            $this->logger->info(sprintf("Command result size is %s bytes", strlen($this->rawResult)), __CLASS__);
            $this->buildResult();
        }
        return $this->result;
    }



    /**
     * @return void
     */
    public function __string()
    {
        echo $this->rawResult;
    }



    /**
     * @return ObjectStorage
     */
    abstract protected function buildResult();
}
