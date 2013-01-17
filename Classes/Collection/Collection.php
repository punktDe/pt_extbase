<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005-2011 Rainer Kuhn, Wolfgang Zenker, 
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
 * Abstract item collection class
 *
 * @author      Rainer Kuhn 
 * @author      Wolfgang Zenker 
 * @author      Fabrizio Branca
 * @author      Michael Knoll
 * @package     Collection
 */
abstract class Tx_PtExtbase_Collection_Collection implements IteratorAggregate, Countable, ArrayAccess {

    /**
     * @var     array   array containing items as values
     */
    protected $itemsArr = array();

    
    
    /**
     * @var     int     uid of last selected collection item
     */
    protected $selectedId;



    /***************************************************************************
     *   CONSTRUCTOR
     **************************************************************************/

    /**
     * Implement the constructor in your inheriting class if you need one
     */
    
    
    
    /***************************************************************************
     *   IMPLEMENTED METHODS: IteratorAggregate INTERFACE API METHODS
     **************************************************************************/

    /**
     * Defined by IteratorAggregate interface: returns an iterator for the object
     *
     * @param   void
     * @return  ArrayIterator     object of type ArrayIterator: Iterator for items within this collection
     */
    public function getIterator() {
        $itemIterator = new ArrayIterator($this->itemsArr);
        return $itemIterator;
    }



    /***************************************************************************
     *   IMPLEMENTED METHODS: Countable INTERFACE API METHODS
     **************************************************************************/

    /**
     * Defined by Countable interface: Returns the number of items
     *
     * @param   void
     * @return  integer     number of items in the items array
     */
    public function count() {
        return count($this->itemsArr);
    }



    /***************************************************************************
     *   IMPLEMENTED METHODS: ArrayAccess INTERFACE API METHODS
     **************************************************************************/

    /**
     * Checks if an offset is in the array
     *
     * @param   mixed   offset
     * @return  bool
     */
    public function offsetExists($offset) {
        return $this->hasItem($offset);
    }

    /**
     * Returns the value for a given offset
     *
     * @param   mixed   offset
     * @return  mixed   element of the collection
     */
    public function offsetGet($offset) {
        return $this->getItemById($offset);
    }
    
    /**
     * Adds an element to the collection
     *
     * @param   mixed   offset
     * @param   mixed   value
     */
    public function offsetSet($offset, $value) {
        $this->addItem($value, $offset);
    }
    
    /**
     * Deletes an element from the collection
     *
     * @param   mixed   offset
     */
    public function offsetUnset($offset) {
        $this->deleteItem($offset);
    }
    
    

    /***************************************************************************
     *  IMPLEMENTED METHODS: array_{shift|unshift|pop|push|keys} equivalents
     **************************************************************************/
    /*
     * array_{shift|unshift|pop|push|keys} functions do not work on objects
     * (even if - like this - they implement the ArrayAccess Interface)
     * Here are the equivalent methods:
     */

	/**
	 * Shift an element off the beginning of the collection
	 *
	 * @param   bool	$doNotModifyKeys (optional) if true key won't be modified, else numerical keys will be renumbered, default if false
	 * @return  mixed   item or NULL if collection is empty
	 */
    public function shift($doNotModifyKeys = false) {
        if (empty($this->itemsArr)) {
            return NULL;
        } elseif ($doNotModifyKeys == true) {
            $keys = array_keys($this->itemsArr);
            $element = $this->itemsArr[$keys[0]];
            unset($this->itemsArr[$keys[0]]);
            return $element;
        } else {
            $keys = array_keys($this->itemsArr);
            if ($this->selectedId == $keys[0]) {
                $this->clearSelectedId();
            }
            return array_shift($this->itemsArr);
        }
    }

    
    
    /**
     * Pop the element off the end of the collection
     *
     * @param   void
     * @return  mixed   item or NULL if collection is empty
     */
    public function pop() {
        if (empty($this->itemsArr)) {
            return NULL;
        } else {
            $keys = array_keys($this->itemsArr);
            if ($this->selectedId == $keys[count($this->itemsArr)-1]) {
                $this->clearSelectedId();
            }
            return array_pop($this->itemsArr);
        }
    }

	/**
	 * Prepend one element to the beginning of the collection
	 * Multiple elements (like in array_unshift) are not supported!
	 *
	 * @param   mixed   $element element to prepend
	 * @param   bool	$doNotModifyKeys (optional) if true key won't be modified, else numerical keys will be renumbered, default if false
	 * @param   mixed   $useKey
	 * @return  int	 (optional) Returns the new number of elements in the collection
	 */
    public function unshift($element, $doNotModifyKeys = false, $useKey = NULL) {
        $this->checkItemType($element);
    
        if ($doNotModifyKeys == true) {
            if (is_null($useKey)) {
                $this->itemsArr = array($element) + $this->itemsArr;
            } else {
                if (array_key_exists($useKey, $this->itemsArr)) {
                    unset($this->itemsArr[$useKey]);
                }
                $this->itemsArr = array($useKey => $element) + $this->itemsArr;
            }
        } else {
            array_unshift($this->itemsArr, $element);
        }
        return $this->count();
    }
    
    
    
    /**
     * Push one or more elements onto the end of collection
     * Multiple elements (like in array_push) are not supported!
     *
     * @param   mixed   element to append
     * @return  int     Returns the new number of elements in the collection
     */
    public function push($element) {
        $this->checkItemType($element);
        
        array_push($this->itemsArr, $element);
        return $this->count();
    }
    
    
    
    /**
     * Return all the ids of this collection
     *
     * @param   mixed   $search_value (optional) if specified, then only keys containing these values are returned.
     * @param   bool    $strict (optional) determines if strict comparision (===) should be used during the search.
     * @return  array   Returns an array of all the keys
     */
    public function keys($search_value = '', $strict = false) {
        if ($search_value != '') {
            $result = array_keys($this->itemsArr, $search_value, $strict);
        } else {
            $result = array_keys($this->itemsArr);
        }
        return $result;
    }



    /***************************************************************************
     *   GENERAL METHODS
     **************************************************************************/

    /**
     * Adds one item to the collection
     *
     * @param   mixed   $itemObj item to add
     * @param   mixed   $id (optional) array key
     * @return  void
     * @throws  Tx_PtExtbase_Exception_Internal   if item to add to collection is of wrong type
     */
    public function addItem($itemObj, $id = 0) {
        // add item if item type is validated
        if ($this->checkItemType($itemObj) == true) {
            if ($id === 0) {
                $this->itemsArr[] = $itemObj;
            } else {
                $this->itemsArr[$id] = $itemObj;
            }
        } else {
			// throw exception if item type is not validated
			throw new Tx_PtExtbase_Exception_Internal('Item to add to collection is of wrong type (' . get_class($itemObj) . '). 1316764449' );
        }
    }
    
    

    /**
     * Deletes one item from the collection
     *
     * @param   mixed   $id of item to remove
     * @return  void
     * @throws  Tx_PtExtbase_Exception_Internal    if trying to delete invalid id
     */
    public function deleteItem($id) {
        if (isset($this->selectedId) && ($id == $this->selectedId)) {
            $this->clearSelectedId();
        }
        if ($this->hasItem($id)) {
            unset($this->itemsArr[$id]);
        } else {
            throw new Tx_PtExtbase_Exception_Internal('Trying to delete invalid id');
        }
    }
    
    

    /**
     * Clears all items of the collection
     *
     * @param   void
     * @return  void
     */
    public function clearItems() {
        $this->clearSelectedId();
        $this->itemsArr = array();
    }
    
    

    /**
     * Checks if item exists in collection
     *
     * @param   mixed   $id key of item to check for existance
     * @return  boolean item with this key exists
     */
    public function hasItem($id) {
        return array_key_exists($id, $this->itemsArr);
    }
    
    

    /**
     * Get item from collection by Id
     *
     * @param   integer     $id of Collection Item
     * @return  mixed       item that has been requested
     * @throws  Tx_PtExtbase_Exception_Internal    if requesting an invalid id
     */
    public function &getItemById($id) {
        if ($this->hasItem($id)) {
            return $this->itemsArr[$id];
        } else {
            throw new Tx_PtExtbase_Exception_Internal(sprintf('Trying to get an invalid id "%s"', $id));
        }
    }
    
    

    /**
     * Get item from collection by Index
     *
     * @param   integer     $idx index (position in array) of Collection Item
     * @return  mixed       item that has been requested
     * @remarks index starts with 0 for first element
     * @throws  Tx_PtExtbase_Exception_Internal if idx is invalid
     */
    public function &getItemByIndex($idx) {
        // check parameters
        $idx = intval($idx);
        if (($idx < 0) || ($idx >= $this->count())) {
            throw new Tx_PtExtbase_Exception_Internal('Invalid index');
        }
        $itemArr = array_values($this->itemsArr);

        return $itemArr[$idx];
    }
    
    

    /**
     * Checks if the type of an item is allowed for the collection - this method should be overwritren by inheriting classes
     *
     * @param   mixed       $itemObj
     * @return  boolean     true by default in this parent class - individual implementations of this method (in inheriting classes) should return the item validation result as true or false
     */
    protected function checkItemType($itemObj) {
        return true;
    }
    
    
    
    /***************************************************************************
     *   PROPERTY GETTER/SETTER METHODS
     **************************************************************************/

    /**
     * Returns the property value
     *
     * @param   void
     * @return  flexible        property value
     */
    public function getSelectedId() {
        return $this->selectedId;
    }
    
    

    /**
     * Sets the property value
     *
     * @param   int		$selectedId
     * @return  void
     * @throws  Tx_PtExtbase_Exception_Internal    when parameter is not a valid item id
     */
    public function setSelectedId($selectedId) {
        if ($this->hasItem($selectedId)) {
            $this->selectedId = $selectedId;
        } else {
            throw new Tx_PtExtbase_Exception_Internal('Invalid id to set');
        }
    }
    
    

    /**
     * Clears the property value
     *
     * @param   void
     * @return  void
     */
    public function clearSelectedId() {
        unset($this->selectedId);
    }
    
}

?>