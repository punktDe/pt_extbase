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
 * Ban URL Command
 *
 * @package PunktDe\PtExtbase\Utility\Varnish\Command
 */
class BanUrlCommand extends GenericShellCommand {

	/**
	 * A list of allowed git command options
	 *
	 * @var array
	 */
	protected $argumentMap = array(
		'url' => '%s'
	);


	/**
	 * @var array
	 */
	protected $arguments = array(
		'url' => '',
	);



	/**
	 * @param string $url
	 * @return $this
	 */
	public function setUrl($url) {
		$this->arguments['url'] = $url;
		return $this;
	}



	/**
	 * @return string
	 */
	public function getCommandName() {
		return 'ban.url';
	}



	/**
	 * @return string
	 */
	protected function buildCommand() {
		$arguments = $this->buildArguments();
		array_unshift($arguments, $this->getCommandName());
		$arguments = array(sprintf("\"%s\"", implode(' ', $arguments)));
		if ($this->subCommand instanceof GenericShellCommand) {
			array_unshift($arguments, $this->subCommand->render());
		}
		return implode(' ', $arguments);
	}

}
