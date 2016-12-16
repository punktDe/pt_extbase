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
 * Log Command
 *
 * @package PunktDe\PtExtbase\Utility\Git\Command
 */
class LogCommand extends GenericShellCommand
{
    /**
     * A list of allowed git command options
     *
     * @var array
     */
    protected $argumentMap = [
        'nameOnly' => '--name-only',
        'format' => '--pretty="%s"',
        'maxCount' => '--max-count=%s'
    ];


    /**
     * @var array
     */
    protected $arguments = [
        'nameOnly' => false,
        'format' => '',
        'maxCount' => ''
    ];



    /**
     * @param boolean $nameOnly
     * @return $this
     */
    public function setNameOnly($nameOnly)
    {
        $this->arguments['nameOnly'] = $nameOnly;
        return $this;
    }



    /**
     * @param string $format
     * @return $this
     */
    public function setFormat($format)
    {
        $this->arguments['format'] = $format;
        return $this;
    }



    /**
     * @param integer $maxCount
     * @return $this
     */
    public function setMaxCount($maxCount)
    {
        $this->arguments['maxCount'] = $maxCount;
        return $this;
    }
}
