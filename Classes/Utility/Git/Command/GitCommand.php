<?php
namespace PunktDe\PtExtbase\Utility\Git\Command;

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

use PunktDe\PtExtbase\Utility\Git\GitClient;

/**
 * Git Command
 *
 * @package PunktDe\PtExtbase\Utility\Git\Command
 */
abstract class GitCommand {

	/**
	 * @var array
	 */
	protected $argumentMap = array();


	/**
	 * @var array
	 */
	protected $arguments = array();


	/**
	 * @var GitClient
	 */
	protected $gitClient;


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
	 * @param GitClient|NULL $gitClient
	 */
	public function __construct($gitClient = NULL) {
		$this->gitClient = $gitClient;
	}



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
		return implode(' ', $arguments);
	}



	/**
	 * @return string
	 */
	public function execute() {
		return $this->gitClient->execute($this);
	}



	/**
	 * @return string
	 */
	public function getCommandName() {
		preg_match('|.*\\\(.+?)Command$|', get_class($this), $matches);
		return strtolower($matches[1]);
	}



	/**
	 * @return string
	 */
	abstract public function render();

}
