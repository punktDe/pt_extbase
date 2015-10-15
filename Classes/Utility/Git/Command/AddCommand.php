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
 * Add Command
 *
 * @package PunktDe\PtExtbase\Utility\Git\Command
 */
class AddCommand extends GenericShellCommand
{
    /**
     * A list of allowed git command options
     *
     * @var array
     */
    protected $argumentMap = array(
        'all' => '--all',
        'path' => '%s'
    );


    /**
     * @var array
     */
    protected $arguments = array(
        'path' => '',
        'all' => false
    );



    /**
     * @param boolean $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->arguments['path'] = $path;
        return $this;
    }


    /**
     * @param boolean $all
     * @return $this
     */
    public function setAll($all)
    {
        $this->arguments['all'] = $all;
        return $this;
    }
}
