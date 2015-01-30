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

/**
 * Void Command
 *
 * @package PunktDe\PtExtbase\Utility\Git
 */
class VoidCommand extends GitCommand {

	/**
	 * @var string
	 */
	protected $command = '';

	/**
	 * A list of allowed git command options
	 *
	 * @var array
	 */
	protected $argumentMap = array(
		'version' => '--version',
	);


	/**
	 * @var array
	 */
	protected $arguments = array(
		'version' => FALSE
	);



	/**
	 * @param boolean $version
	 */
	public function setVersion($version) {
		$this->arguments['version'] = $version;
	}


	/**
	 * @return string
	 */
	public function render() {
		return $this->buildCommand();
	}

} 