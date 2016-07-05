<?php
namespace PunktDe\PtExtbase\Exception;

/***************************************************************
*  Copyright notice
*  
*  (c) 2005-2016
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

/** 
 * "Internal error" exception class
 */
class InternalException extends Exception
{
    /**
     * Class constructor
     * 
     * @param   string  $errMsg error message (used for frontend/enduser display, too)    
     * @param   string  $debugMsg detailed debug message (not used for frontend display)  
     */
    public function __construct($errMsg='', $debugMsg='')
    {
        parent::__construct($errMsg, 3, $debugMsg);
    }
}
