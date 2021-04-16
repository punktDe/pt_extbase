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

use Neos\Utility\Files;
use PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\ExecutionManager;
use PunktDe\PtExtbase\Utility\Git\Command;

/**
 * Git Execution Manager
 *
 * @package PunktDe\PtExtbase\Utility\Git
 */
class GitExecutionManager extends ExecutionManager
{
    /**
     * @var \PunktDe\PtExtbase\Utility\Git\GitRepository
     */
    protected $repository;


    /**
     * @var string
     */
    protected $commandLine = '';


    /**
     * @return string
     */
    protected function renderCommand()
    {
        $this->commandLine = sprintf('cd %s; %s --git-dir=%s %s', $this->repository->getRepositoryRootPath(), $this->repository->getCommandPath(), Files::concatenatePaths([$this->repository->getRepositoryRootPath(), '.git']), $this->command->render());
    }



    /**
     * @param \PunktDe\PtExtbase\Utility\Git\GitRepository $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }
}
