<?php
namespace PunktDe\PtExtbase\Exception;

/***************************************************************
 *  Copyright (C) 2014-2016 punkt.de GmbH
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

use TYPO3\CMS\Core\Log\LogLevel;


class LoggerException extends \Exception
{
    /**
     * @var integer
     * @see \TYPO3\CMS\Core\Log\LogLevel
     */
    protected $logLevel;


    /**
     * @param string $message
     * @param int $code
     * @param \Exception|int $logLevel
     * @param \Exception|null $previous
     */
    public function __construct($message = "", $code = 0, $logLevel = LogLevel::ERROR, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        if (LogLevel::isValidLevel($logLevel)) {
            $this->logLevel = $logLevel;
        } else {
            $this->logLevel = LogLevel::ERROR;
        }
    }
    
    /**
     * @return integer
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }
}
