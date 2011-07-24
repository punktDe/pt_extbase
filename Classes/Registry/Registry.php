<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2008 Fabrizio Branca <mail@fabrizio-branca.de>,
*           Michael Knoll <knoll@punkt.de>
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
 * Singleton registry
 * 
 * @author	Fabrizio Branca <mail@fabrizio-branca.de>
 * @author  Michael Knoll
 * @see 	http://www.patternsforphp.com/wiki/Registry
 */
final class Tx_PtExtbase_Registry_Registry extends Tx_PtExtbase_Collection_Collection {
	
	
	/**
	 * @var 	Unique instance of this class
	 */
	private static $uniqueInstance = NULL;
	

	
    /**
     * Returns a unique instance of the Singleton object. Use this method instead of the private/protected class constructor.
     * 
     * @param   void
     * @return  Tx_PtExtbase_Registry_Registry      unique instance of the Singleton object
     * @author 	Fabrizio Branca <mail@fabrizio-branca.de>
     */
    public static function getInstance() {
        if (self::$uniqueInstance === NULL) {
            self::$uniqueInstance = new Tx_PtExtbase_Registry_Registry();
        } 
        return self::$uniqueInstance;
    }
    
    
    
    /**
     * Final method to prevent object cloning (using 'clone'), in order to use only the unique instance of the Singleton object.
     * 
     * @param   void
     * @return  void
     */
    public final function __clone() {
        trigger_error('Clone is not allowed for '.get_class($this).' (Singleton)', E_USER_ERROR);
    }
    
    

    /**
     * Adds one item to the collection
     *
     * @param	object	object to add
     * @param	mixed	array key / label (use namespaces here to avoid conflicts!)
     * @param	bool	(optional) overwrite existing object, default is false
     * @return	void
     * @throws	Exception	if the given label already exists and overwrite if false
     */
    public function addItem($object, $label, $overwrite = false) {
    	Tx_PtExtbase_Assertions_Assert::isNotEmpty($label, array('message' => 'Registry keys cannot be empty!'));
    	
    	if (!$this->hasItem($label) || $overwrite == true) {
    		
    		// add object to the collection
            parent::addItem($object, $label);
        } else {
        	throw new Exception('There is already an element stored with the label "'.$label.'" (and overwriting not permitted)!');
        }
    }
    
    
    
    /**
     * Registers an object to the registry
     *
     * @param 	mixed	$label label, use namespaces here to avoid conflicts
     * @param 	mixed 	$object object
     * @param	bool	$overwrite (optional) overwrite existing object, default is false
     * @return 	void
     * @throws	Exception	if the given label already exists and overwrite if false
     */
    public function register($label, $object, $overwrite = false) {
    	// swapping $label (id) and $object parameters 
    	$this->addItem($object, $label, $overwrite);
    }
    
    
    
    /**
     * Unregisters a label
     *
     * @param 	mixed 	label
     * @throws	Exception 	if the label does not exists (uncaught exception from "deleteItem")
     */
    public function unregister($label) {
       	$this->deleteItem($label);
    }
 
    
    
    /**
     * Gets the object for a given label
     *
     * @param 	mixed	label
     * @return 	mixed	object
     * @throws	Exception 	if the label does not exists (uncaught exception from "getItemById")
     */
    public function get($label) {
    	return $this->getItemById($label);
    }
 
    
    
    /**
     * Checks if the label exists
     *
     * @param 	mixed	label
     * @return 	bool
     */
    public function has($label) {
        return $this->hasItem($label);
    }
    
    
    /***************************************************************************
	 * Magic methods wrappers for registry pattern methods
	 * 
	 * $reg = tx_pttools_registry::getInstance();
	 * $reg->myObject = new SomeObject();
	 * if (isset($reg->myObject)) {
	 * 		// there is a myObject value
	 * } else {
	 * 		// there is not a myObject value
	 * }
	 * $obj = $reg->myObject;
	 * unset($reg->myObject);
	 **************************************************************************/
    
    /**
     * @see 	Tx_PtExtbase_Registry_Registry::register
     */
    public function __set($label, $object) {
    	$this->register($label, $object);
    }
    
    
    
    /**
     * @see 	Tx_PtExtbase_Registry_Registry::unregister
     */
    public function __unset($label) {
        $this->unregister($label);
    }
    
    
    
    /**
     * @see 	Tx_PtExtbase_Registry_Registry::get
     */
    public function __get($label) {
        return $this->get($label);
    }
    
    
    
    /**
     * @see 	Tx_PtExtbase_Registry_Registry::has
     */
    public function __isset($label) {
    	return $this->has($label);
    }
    
    
}

?>