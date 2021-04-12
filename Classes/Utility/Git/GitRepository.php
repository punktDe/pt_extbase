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

use PunktDe\PtExtbase\Logger\Logger;
use PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\GenericShellCommand;
use Neos\Utility\Files;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Git Repository
 *
 * @package PunktDe\PtExtbase\Utility\Git
 */
class GitRepository
{

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var GitExecutionManager
     */
    protected $gitExecutionManager;


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
     * @param GitExecutionManager $gitExecutionManager
     */
    public function injectGitExecutionManager(GitExecutionManager $gitExecutionManager): void
    {
        $this->gitExecutionManager = $gitExecutionManager;
    }


    /**
     * @var string
     */
    protected $commandPath = '/usr/bin/git';


    /**
     * @var string
     */
    protected $repositoryRootPath = '.';


    /**
     * @param string $commandPath
     * @param string $repositoryRootPath
     */
    public function __construct($commandPath, $repositoryRootPath)
    {
        $this->commandPath = $commandPath;
        $this->repositoryRootPath = $repositoryRootPath;
    }



    /**
     * @return void
     */
    public function initializeObject()
    {
        $this->createRepositoryRootPath();
        $this->checkIfValidGitCommandIsAvailable();
    }



    /**
     * @return void
     */
    public function createRepositoryRootPath()
    {
        Files::createDirectoryRecursively($this->repositoryRootPath);
    }



    /**
     * @return void
     * @throws \Exception
     */
    protected function checkIfValidGitCommandIsAvailable()
    {
        if (!file_exists($this->commandPath) || strpos($this->void()->setVersion(true)->execute()->getRawResult(), 'git') !== 0) {
            throw new \Exception("No valid git command found on system in path " . $this->commandPath, 1422469432);
        }
    }



    /**
     * @return Command\StatusCommand
     */
    public function status()
    {
        return $this->createCommandForRepository('Status');
    }



    /**
     * @return Command\LogCommand
     */
    public function log()
    {
        return $this->createCommandForRepository('Log');
    }



    /**
     * @return Command\AddCommand
     */
    public function add()
    {
        return $this->createCommandForRepository('Add');
    }



    /**
     * @return Command\CommitCommand
     */
    public function commit()
    {
        return $this->createCommandForRepository('Commit');
    }



    /**
     * @return Command\TagCommand
     */
    public function tag()
    {
        return $this->createCommandForRepository('Tag');
    }



    /**
     * @return Command\PushCommand
     */
    public function push()
    {
        return $this->createCommandForRepository('Push');
    }



    /**
     * @return Command\InitCommand
     */
    public function init()
    {
        return $this->createCommandForRepository('Init');
    }



    /**
     * @return Command\RemoteCommand
     */
    public function remote()
    {
        return $this->createCommandForRepository('Remote');
    }



    /**
     * @return Command\ConfigCommand
     */
    public function config()
    {
        return $this->createCommandForRepository('Config');
    }



    /**
     * @return Command\CheckoutCommand
     */
    public function checkout()
    {
        return $this->createCommandForRepository('Checkout');
    }



    /**
     * Clone Repository
     *
     * "clone" is a PHP key word. Thus, "cloneRepository" is used instead.
     *
     * @return Command\CloneCommand
     */
    public function cloneRepository()
    {
        return $this->createCommandForRepository('Clone');
    }



    /**
     * @return boolean
     */
    public function exists()
    {
        if ($this->status()->execute()->getExitCode() === 128) {
            return false;
        }
        return true;
    }



    /**
     * @return Command\VoidCommand
     */
    protected function void()
    {
        return $this->createCommandForRepository('Void');
    }


    
    /**
     * @param string $commandName
     * @return GenericShellCommand
     */
    protected function createCommandForRepository($commandName)
    {
        $this->gitExecutionManager->setRepository($this);
        return $this->objectManager->get(sprintf("PunktDe\\PtExtbase\\Utility\\Git\\Command\\%sCommand", $commandName));
    }



    /**
     * @return string
     */
    public function getRepositoryRootPath()
    {
        return $this->repositoryRootPath;
    }



    /**
     * @return string
     */
    public function getCommandPath()
    {
        return $this->commandPath;
    }
}
