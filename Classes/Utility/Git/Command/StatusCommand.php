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
 * Status Command
 *
 * @package PunktDe\PtExtbase\Utility\Git\Command
 */
class StatusCommand extends GenericShellCommand
{
    /**
     * @var array
     */
    protected $argumentMap = [
        'short' => '--short',
        'untrackedFilesMode' => '--untracked-files=%s'
    ];


    /**
     * @var array
     */
    protected $arguments = [
        'short' => false,
        'untrackedFilesMode' => 'all'
    ];



    /**
     * @param boolean $short
     * @return $this
     */
    public function setShort($short)
    {
        $this->arguments['short'] = $short;
        return $this;
    }



    /**
     * Set untracked files mode
     *
     * - all (default): Also shows individual files in untracked directories
     * - normal: Shows untracked files and directories
     * - no: Show no untracked files.
     *
     * @param string $mode
     * @return $this
     */
    public function setUntrackedFilesMode($mode)
    {
        $this->arguments['untrackedFilesMode'] = $mode;
        return $this;
    }



    /**
     * @return boolean
     */
    public function isShort()
    {
        return $this->arguments['short'];
    }
}
