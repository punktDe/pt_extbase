<?php
namespace PunktDe\PtExtbase\Utility\Git;

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

use PunktDe\PtExtbase\Utility\Files;
use PunktDe\PtExtbase\Utility\Git\Result\Result;
use PunktDe\PtExtbase\Utility\Git\Command;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Git Execution Manager
 *
 * @package PunktDe\PtExtbase\Utility\Git
 */
class GitExecutionManager implements SingletonInterface {

	/**
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;


	/**
	 * @inject
	 * @var \PunktDe\PtExtbase\Utility\ShellCommandService
	 */
	protected $shellCommandService;


	/**
	 * @inject
	 * @var \Tx_PtExtbase_Logger_Logger
	 */
	protected $logger;


	/**
	 * @var \PunktDe\PtExtbase\Utility\Git\GitRepository
	 */
	protected $repository;


	/**
	 * @var \PunktDe\PtExtbase\Utility\Git\Command\GitCommand
	 */
	protected $gitCommand;


	/**
	 * @var string
	 */
	protected $commandLine = '';


	/**
	 * @param Command\GitCommand $gitCommand
	 * @return string
	 */
	public function execute($gitCommand) {
		$this->gitCommand = $gitCommand;
		$this->renderCommand();
		return array($this->executeCommandLineOnShell(), $this->shellCommandService->getExitCode());
	}



	/**
	 * @return string
	 */
	protected function renderCommand() {
		$this->commandLine = sprintf('%s --git-dir=%s %s ', $this->repository->getCommandPath(), Files::concatenatePaths(array($this->repository->getRepositoryRootPath(), '.git')), $this->gitCommand->render());
    }



	/**
	 * @throws GitException
	 * @return string
	 */
	protected function executeCommandLineOnShell() {
		$this->logger->info(sprintf("Running git command %s", $this->commandLine), __CLASS__);
		return $this->shellCommandService->execute($this->commandLine);
	}



	/**
	 * @param \PunktDe\PtExtbase\Utility\Git\GitRepository $repository
	 */
	public function setRepository($repository) {
		$this->repository = $repository;
	}

}
