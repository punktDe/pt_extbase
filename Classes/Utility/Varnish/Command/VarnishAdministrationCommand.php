<?php
namespace PunktDe\PtExtbase\Utility\Varnish\Command;

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

use PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\GenericShellCommand;

/**
 * Varnish Administration Command
 *
 * @package PunktDe\PtExtbase\Utility\Varnish\Command
 */
class VarnishAdministrationCommand extends GenericShellCommand {

	/**
	 * @var array
	 */
	protected $argumentMap = array(
		'secretFile' => '-S %s',
		'address' => '%s',
		'port' => '%s'
	);


	/**
	 * @var array
	 */
	protected $arguments = array(
		'secretFile' => '',
		'address' => '',
		'port' => ''
	);



	/**
	 * @param string $secretFile
	 * @return $this
	 */
	public function setSecretFile($secretFile) {
		$this->arguments['secretFile'] = $secretFile;
		return $this;
	}



	/**
	 * @param string $address
	 * @return $this
	 */
	public function setAddress($address) {
		$this->arguments['address'] = $address;
		return $this;
	}



	/**
	 * @param string $port
	 * @return $this
	 */
	public function setPort($port) {
		$this->arguments['portMode'] = $port;
		return $this;
	}



	/**
	 * @return BanUrlCommand
	 */
	public function add() {
		$command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Varnish\Command\Remote\BanUrlCommand');
		$command->attachCommand($this);
		return $command;
	}



	/**
	 * @return string
	 */
	public function render() {
		return sprintf("%s", $this->buildCommand());
	}



	/**
	 * @return string
	 */
	public function getCommandName() {
		return 'varnishadm';
	}

}
