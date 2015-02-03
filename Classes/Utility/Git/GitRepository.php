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

use PunktDe\PtExtbase\Utility\Git\Command;

/**
 * Git Repository
 *
 * @package PunktDe\PtExtbase\Utility\Git
 */
class GitRepository {

	/**
	 * @inject
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;


	/**
	 * @inject
	 * @var \Tx_PtExtbase_Logger_Logger
	 */
	protected $logger;


	/**
	 * @inject
	 * @var \PunktDe\PtExtbase\Utility\Git\GitExecutionManager
	 */
	protected $gitExecutionManager;


	/**
	 * @var string
	 */
	protected $commandPath;


	/**
	 * @var string
	 */
	protected $repositoryRootPath;


	/**
	 * @param string $commandPath
	 * @param string $repositoryRootPath
	 */
	public function __construct($commandPath, $repositoryRootPath) {
		$this->commandPath = $commandPath;
		$this->repositoryRootPath = $repositoryRootPath;
	}



	/**
	 * @return void
	 */
	public function initializeObject() {
		$this->setCommandPath($this->commandPath);
		$this->setRepositoryRootPath($this->repositoryRootPath);
		$this->checkIfValidGitCommandIsAvailable();
	}



	/**
	 * @return void
	 * @throws \Exception
	 */
	protected function checkIfValidGitCommandIsAvailable() {
		if (!file_exists($this->gitExecutionManager->getCommandPath()) || strpos($this->void()->setVersion(TRUE)->execute()->getRawResult(), 'git') !== 0) {
			throw new \Exception("No valid git command found on system", 1422469432);
		}
	}



	/**
	 * @return Command\StatusCommand
	 */
	public function status() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\StatusCommand');
	}



	/**
	 * @return Command\LogCommand
	 */
	public function log() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\LogCommand');
	}



	/**
	 * @return Command\AddCommand
	 */
	public function add() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\AddCommand');
	}



	/**
	 * @return Command\CommitCommand
	 */
	public function commit() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\CommitCommand');
	}



	/**
	 * @return Command\TagCommand
	 */
	public function tag() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\TagCommand');
	}



	/**
	 * @return Command\PushCommand
	 */
	public function push() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\PushCommand');
	}



	/**
	 * @return Command\InitCommand
	 */
	public function init() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\InitCommand');
	}



	/**
	 * @return Command\RemoteCommand
	 */
	public function remote() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\RemoteCommand');
	}



	/**
	 * @return Command\VoidCommand
	 */
	protected function void() {
		return $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\VoidCommand');
	}



	/**
	 * @param string $commandPath
	 */
	public function setCommandPath($commandPath) {
		$this->gitExecutionManager->setCommandPath($commandPath);
	}



	/**
	 * @param string $repositoryRootPath
	 */
	public function setRepositoryRootPath($repositoryRootPath) {
		$this->gitExecutionManager->setRepositoryRootPath($repositoryRootPath);
	}

}
