<?php
namespace PunktDe\PtExtbase\Utility\Varnish;

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

use PunktDe\PtExtbase\Utility\Varnish\Command\VarnishAdministrationCommand;
use PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\AbstractExecutionManager;

/**
 * Git Execution Manager
 *
 * @package PunktDe\PtExtbase\Utility\Git
 */
class ExecutionManager extends AbstractExecutionManager {

	/**
	 * @var VarnishAdministrationCommand
	 */
	protected $varnishAdministrationCommand;


	/**
	 * @var string
	 */
	protected $varnishAdministrationCommandPath;


	/**
	 * @var string
	 */
	protected $commandLine = '';


	/**
	 * @param VarnishAdministrationCommand $varnishAdministrationCommand
	 * @return string
	 */
	public function execute($varnishAdministrationCommand) {
		$this->varnishAdministrationCommand = $varnishAdministrationCommand;
		$this->renderCommand();
		return array($this->executeCommandLineOnShell(), $this->shellCommandService->getExitCode());
	}



	/**
	 * @return string
	 */
	protected function renderCommand() {
		$this->commandLine = sprintf('%s', $this->varnishAdministrationCommand->render());
    }



	/**
	 * @return string
	 */
	protected function executeCommandLineOnShell() {
		$this->logger->debug(sprintf("Running command %s", $this->commandLine), __CLASS__);
		$this->shellCommandService->setRedirectStandardErrorToStandardOut(TRUE);
		return $this->shellCommandService->execute($this->commandLine);
	}


}
