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
 * Init Command
 *
 * @package PunktDe\PtExtbase\Utility\Git\Command
 */
class InitCommand extends GenericShellCommand
{
    /**
     * A list of allowed git command options
     *
     * @var array
     */
    protected $argumentMap = array(
        'bare' => '--bare',
        'shared' => '--shared'
    );


    /**
     * @var array
     */
    protected $arguments = array(
        'bare' => false,
        'shared' => false
    );


    /**
     * @param boolean $bare
     * @return $this
     */
    public function setBare($bare)
    {
        $this->arguments['bare'] = $bare;
        return $this;
    }



    /**
     * @param boolean $shared
     * @return $this
     */
    public function setShared($shared)
    {
        $this->arguments['shared'] = $shared;
        return $this;
    }
}
