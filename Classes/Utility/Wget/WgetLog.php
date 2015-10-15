<?php
 /***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lienert <lienert@punkt.de>
 *
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

namespace PunktDe\PtExtbase\Utility\Wget;

class WgetLog extends \Tx_PtExtbase_Collection_ObjectCollection
{
    /**
     * @var string
     */
    protected $restrictedClassName = '\PunktDe\PtExtbase\Utility\Wget\WgetLogEntry';


    /**
     * @param WgetLogEntry $wgetLogEntry
     * @throws \Tx_PtExtbase_Exception_Internal
     */
    public function addLogEntry(WgetLogEntry $wgetLogEntry)
    {
        $this->addItem($wgetLogEntry);
    }


    /**
     * @return bool
     */
    public function hasErrors()
    {
        return $this->getErrors()->count() > 0 ?: false;
    }


    /**
     * @return WgetLog
     */
    public function getErrors()
    {
        $errorEntries = new WgetLog();

        foreach ($this->itemsArr as $logEntry) {
            if ($logEntry->isError()) {
                $errorEntries->addLogEntry($logEntry);
            }
        }

        return $errorEntries;
    }


    /**
     * @return WgetLog
     */
    public function getSuccessful()
    {
        $sucessfulEntries = new WgetLog();

        foreach ($this->itemsArr as $logEntry) {
            if (!$logEntry->isError()) {
                $sucessfulEntries->addLogEntry($logEntry);
            }
        }

        return $sucessfulEntries;
    }


    /**
     * @return array
     */
    public function toArray()
    {
        $logArray = array();

        foreach ($this->itemsArr as $logEntry) { /** @var \PunktDe\PtExtbase\Utility\Wget\WgetLogEntry $logEntry */
            $logArray[] = $logEntry->toArray();
        }

        return $logArray;
    }
}
