<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2011 Wolfgang Zenker, Rainer Kuhn,
*                Fabrizio Branca, Michael Knoll
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
 * Abstract object collection class. Object collection can be restricted to contain
 * only a certain type of objects.
 *
 * @author      Rainer Kuhn 
 * @author      Wolfgang Zenker 
 * @author      Fabrizio Branca
 * @author      Michael Knoll
 * @package     Collection
 */
abstract class Tx_PtExtbase_Collection_ObjectCollection extends Tx_PtExtbase_Collection_Collection  {

    /**
     * if set, added objects will be type checked against this classname - 
     * this property should be set by your inheriting class if want to check the object type when adding an item
     * 
     * @var     string  
     */
    protected $restrictedClassName = NULL;

    

    /**
     * Checks if the type of an item object matches the restrictedClassName 
     * (this property should be set in your inheriting class if want to check the object type when adding an item)
     * 
     * Template method that overwrites behaviour of base class so that all
     * items added to the collection are checked to implement / extend 
     * the type given in restrictedClassName property
     *
     * @param   mixed       object item to validate
     * @return  boolean     true if object validation suceeded, false otherwise
     */
    final protected function checkItemType($itemObj) {
        if (!is_null($this->restrictedClassName) && !($itemObj instanceof $this->restrictedClassName)) {
            return false;
        }
        return true;
    }
}

?>