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

use PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\AbstractResult;

/**
 * Generic Shell Command
 *
 * @package PunktDe\PtExtbase\Utility\GenericShellCommandWrapper
 */
class GenericShellCommand {

	/**
	 * A list of allowed command options
	 *
	 * @var array
	 */
	protected $argumentMap = array();


	/**
	 * @var array
	 */
	protected $arguments = array();


	/**
	 * @var string
	 */
	protected $commandName = '';


	/**
	 * @var GenericShellCommand
	 */
	protected $subCommand;


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
	 * @return string
	 */
	protected function buildArguments() {
		$arguments = array();

		foreach($this->argumentMap as $propertyName => $argumentTemplate) {
			if(array_key_exists($propertyName, $this->arguments) && !empty($this->arguments[$propertyName]) && $this->arguments[$propertyName] !== FALSE) {
				if(stristr($argumentTemplate, '%s') === FALSE) {
					$arguments[] = $argumentTemplate;
				} else {
					$arguments[] = sprintf($argumentTemplate, $this->arguments[$propertyName]);
				}
			}
		}
		return $arguments;
	}



	/**
	 * @return string
	 */
	protected function buildCommand() {
		$arguments = $this->buildArguments();
		array_unshift($arguments, $this->getCommandName());
		if ($this->subCommand instanceof GenericShellCommand) {
			array_unshift($arguments, $this->subCommand->render());
		}
		return implode(' ', $arguments);
	}



	/**
	 * @return AbstractResult
	 */
	public function execute() {
		return $this->objectManager->get($this->getResultType(), $this);
	}



	/**
	 * @return string
	 */
	public function getCommandName() {
		if (empty($this->commandName)) {
			preg_match('|.*\\\(.+?)Command$|', $this->getClass(), $matches);
			$this->commandName = strtolower($matches[1]);
		}
		return $this->commandName;
	}



	/**
	 * @return string
	 */
	public function getResultType() {
		preg_match('|(.+)\\\Command\\\.+?Command$|', $this->getClass(), $matches);
		$className = sprintf("%s\\Result\\%sResult", $matches[1], ucfirst($this->getCommandName()));
		if (class_exists($className)) {
			return $className;
		}
		return sprintf("%s\\Result\\Result", $matches[1]);
	}



	/**
	 * @param GenericShellCommand $command
	 * @return void
	 */
	protected function attachCommand(GenericShellCommand $command) {
		$this->subCommand = $command;
	}



	/**
	 * Adapter method for test mocks
	 *
	 * @return string
	 */
	protected function getClass() {
		return  get_class($this);
	}



	/**
	 * @return string
	 */
	public function render() {
		return $this->buildCommand();
	}

}
