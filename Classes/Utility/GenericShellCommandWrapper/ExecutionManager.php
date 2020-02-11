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
use PunktDe\PtExtbase\Utility\ShellCommandService;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Execution Manager
 *
 * @package PunktDe\PtExtbase\Utility\GenericShellCommandWrapper
 */
class ExecutionManager implements SingletonInterface
{

    /**
     * @var ShellCommandService
     */
    protected $shellCommandService;

    /**
     * @var Logger
     */
    protected $logger;


    /**
     * @param ShellCommandService $shellCommandService
     */
    public function injectShellCommandService(ShellCommandService $shellCommandService): void
    {
        $this->shellCommandService = $shellCommandService;
    }

    /**
     * @param Logger $logger
     */
    public function injectLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }


    /**
     * @var GenericShellCommand
     */
    protected $command;


    /**
     * @var string
     */
    protected $commandLine = '';


    /**
     * @param GenericShellCommand $command
     * @return string
     */
    public function execute($command)
    {
        $this->command = $command;
        $this->renderCommand();
        return [$this->executeCommandLineOnShell(), $this->shellCommandService->getExitCode()];
    }



    /**
     * @return string
     */
    protected function renderCommand()
    {
        $this->commandLine = sprintf('%s', $this->command->render());
    }



    /**
     * @return string
     */
    protected function executeCommandLineOnShell()
    {
        $this->logger->debug(sprintf("Running command %s", $this->commandLine), __CLASS__);
        $this->shellCommandService->setRedirectStandardErrorToStandardOut(true);
        return $this->shellCommandService->execute($this->commandLine);
    }
}
