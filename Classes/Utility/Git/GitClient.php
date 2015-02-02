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

use PunktDe\PtExtbase\Utility\ShellCommandService;
use PunktDe\PtExtbase\Utility\Git\Result\Result;
use PunktDe\PtExtbase\Utility\Git\Command;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * Git Client
 *
 * @package PunktDe\PtExtbase\Utility\Git
 */
class GitClient implements SingletonInterface {

	/**
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;


	/**
	 * @inject
	 * @var ShellCommandService
	 */
	protected $shellCommandService;


	/**
	 * @inject
	 * @var \Tx_PtExtbase_Logger_Logger
	 */
	protected $logger;


	/**
	 * @var string
	 */
	protected $commandPath = '/usr/bin/git';


	/**
	 * @var string
	 */
	protected $repositoryRootPath = '~';



	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->checkIfValidGitCommandIsAvailable();
	}



	/**
	 * @return void
	 * @throws \Exception
	 */
	protected function checkIfValidGitCommandIsAvailable() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\VoidCommand'); /** @var \PunktDe\PtExtbase\Utility\Git\Command\VoidCommand $command */
		$command->setVersion(TRUE);
		if (!file_exists($this->commandPath) || strpos($this->execute($command)->getRawResult(), 'git') !== 0) {
			throw new \Exception("No valid git command found on system", 1422469432);
		}
	}



	/**
	 * @return Command\StatusCommand
	 */
	public function status() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\StatusCommand', $this);
	}



	/**
	 * @return Command\LogCommand
	 */
	public function log() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\LogCommand', $this);
	}



	/**
	 * @return Command\AddCommand
	 */
	public function add() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\AddCommand', $this);
	}



	/**
	 * @return Command\CommitCommand
	 */
	public function commit() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\CommitCommand', $this);
	}



	/**
	 * @return Command\TagCommand
	 */
	public function tag() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\TagCommand', $this);
	}



	/**
	 * @return Command\PushCommand
	 */
	public function push() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\PushCommand', $this);
	}



	/**
	 * @return Command\InitCommand
	 */
	public function init() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\InitCommand', $this);
	}



	/**
	 * @param Command\GitCommand $gitCommand
	 * @return Result
	 */
	public function execute($gitCommand) {
		$commandLine = $this->renderCommand($gitCommand);
		$this->logger->info($commandLine);
		// return $this->shellCommandService->execute($commandLine);
		return $this->objectManager->get($gitCommand->getResultClassName(), $this->shellCommandService->execute($commandLine));
	}



	/**
	 * @param Command\GitCommand $gitCommand
	 * @return string
	 */
	protected function renderCommand($gitCommand) {
		return sprintf('cd %s; %s %s', $this->repositoryRootPath, $this->commandPath, $gitCommand->render());
    }



	/**
	 * @param string $commandPath
	 */
	public function setCommandPath($commandPath) {
		$this->commandPath = $commandPath;
	}



	/**
	 * @param string $repositoryRootPath
	 */
	public function setRepositoryRootPath($repositoryRootPath) {
		$this->repositoryRootPath = $repositoryRootPath;
	}

}
