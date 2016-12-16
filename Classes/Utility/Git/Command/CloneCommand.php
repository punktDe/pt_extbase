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
 * Clone Command
 *
 * @package PunktDe\PtExtbase\Utility\Git\Command
 */
class CloneCommand extends GenericShellCommand
{
    /**
     * A list of allowed git command options
     *
     * @var array
     */
    protected $argumentMap = [
        'branch' => '--branch %s',
        'depth' => '--depth %s',
        'repository' => '%s',
        'directory' => '%s',
    ];


    /**
     * @var array
     */
    protected $arguments = [
        'branch' => '',
        'depth' => '',
        'repository' => '',
        'directory' => ''
    ];



    /**
     * @param string $branch
     * @return $this
     */
    public function setBranch($branch)
    {
        $this->arguments['branch'] = $branch;
        return $this;
    }



    /**
     * @param integer $depth
     * @return $this
     */
    public function setDepth($depth)
    {
        $this->arguments['depth'] = $depth;
        return $this;
    }



    /**
     * @param string $repository
     * @return $this
     */
    public function setRepository($repository)
    {
        $this->arguments['repository'] = $repository;
        return $this;
    }



    /**
     * @param string $directory
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->arguments['directory'] = $directory;
        return $this;
    }
}
