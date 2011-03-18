<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2008 - 2011 Rainer Kuhn, Michael Knoll
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
 * "Assertion failed" exception class
 *
 * @author      Fabrizio Branca
 * @author      Michael Knoll
 * @package     Exception
 */ 
class Tx_PtExtbase_Exception_Assertion extends Tx_PtExtbase_Exception_Exception {
     
    /**
     * Class constructor
     * 
     * @param   string $errMsg  error message (used for frontend/enduser display, too)    
     * @param   string $debugMsg detailed debug message (not used for frontend display)  
     */
    public function __construct($errMsg='', $debugMsg='') {
        
        parent::__construct($errMsg, 0, $debugMsg);
        
    }
    
    

    /**
     * Set file
     *  
     * @param string $file
     * @return void
     */
    public function setFile($file) {
        $this->file = $file;
    }
    
    
    
    /**
     * Set line
     *  
     * @param string line
     * @return void
     */
    public function setLine($line) {
        $this->line = $line;
    }
    
}

?>