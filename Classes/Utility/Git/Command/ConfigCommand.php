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

/**
 * Config Command
 *
 * @package PunktDe\PtExtbase\Utility\Git\Command
 */
class ConfigCommand extends GitCommand {

	/**
	 * A list of allowed git command options
	 *
	 * @var array
	 */
	protected $argumentMap = array(
		'global' => '--global',
		'username' => 'user.name "%s"',
		'email' => 'user.email "%s"'
	);


	/**
	 * @var array
	 */
	protected $arguments = array(
		'global' => FALSE,
		'username' => '',
		'email' => ''
	);


	/**
	 * @param boolean $global
	 * @return $this
	 */
	public function setGlobal($global) {
		$this->arguments['global'] = $global;
		return $this;
	}



	/**
	 * @param string $username
	 * @return $this
	 */
	public function setUserName($username) {
		$this->arguments['username'] = $username;
		return $this;
	}



	/**
	 * @param string $email
	 * @return $this
	 */
	public function setEmail($email) {
		$this->arguments['email'] = $email;
		return $this;
	}



	/**
	 * @return string
	 */
	public function render() {
		return $this->buildCommand();
	}


}
