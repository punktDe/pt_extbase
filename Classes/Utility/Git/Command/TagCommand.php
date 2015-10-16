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
 * Tag Command
 *
 * @package PunktDe\PtExtbase\Utility\Git\Command
 */
class TagCommand extends GenericShellCommand
{
    /**
     * A list of allowed git command options
     *
     * @var array
     */
    protected $argumentMap = array(
        'sign' => '-s',
        'annotate' => '-a',
        'message' => '-m "%s"'
    );


    /**
     * @var array
     */
    protected $arguments = array(
        'sign' => 'FALSE'
    );



    /**
     * @param boolean $sign
     * @return $this
     */
    public function setSign($sign)
    {
        $this->arguments['sign'] = $sign;
        return $this;
    }



    /**
     * @param boolean $annotate
     * @return $this
     */
    public function setAnnotate($annotate)
    {
        $this->arguments['annotate'] = $annotate;
        return $this;
    }



    /**
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->arguments['message'] = $message;
        return $this;
    }



    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->arguments['name'] = $name;
        return $this;
    }



    /**
     * @return string
     */
    public function render()
    {
        $command[] = $this->buildCommand();
        $command[] = $this->arguments['name'];
        return implode(' ', $command);
    }
}
