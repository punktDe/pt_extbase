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

use PunktDe\PtExtbase\Utility\GenericShellCommandWrapper\GenericShellCommand;

/**
 * Remote Command
 *
 * @package PunktDe\PtExtbase\Utility\Git\Command
 */
class RemoteCommand extends GenericShellCommand
{
    /**
     * A list of allowed git command options
     *
     * @var array
     */
    protected $argumentMap = array(
        'remote' => '%s',
        'refspec' => '%s'
    );


    /**
     * @var array
     */
    protected $arguments = array(
        'remote' => '',
        'refspec' => ''
    );


    /**
     * @return Remote\AddCommand
     */
    public function add()
    {
        $command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\Remote\AddCommand');
        $command->attachCommand($this);
        return $command;
    }



    /**
     * @return Remote\RemoveCommand
     */
    public function remove()
    {
        $command = $this->objectManager->get('PunktDe\PtExtbase\Utility\Git\Command\Remote\RemoveCommand');
        $command->attachCommand($this);
        return $command;
    }



    /**
     * @return string
     */
    public function render()
    {
        return sprintf("%s", $this->buildCommand());
    }
}
