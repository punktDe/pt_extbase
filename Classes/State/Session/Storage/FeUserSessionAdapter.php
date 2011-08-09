<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2005-2008 Rainer Kuhn (kuhn@punkt.de)
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
 * Session Storage Adapter class for TYPO3 FRONTEND _user_ sessions 
 *
 * @author      Rainer Kuhn <kuhn@punkt.de>
 * @author      Michael Knoll <knoll@punkt.de>
 * @package     State
 * @subpackage  Session\Storage
 */
class Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter implements Tx_PtExtbase_State_Session_Storage_AdapterInterface {
	
    /**
     * Holds singleton instance of this class
     *
     * @var Tx_PtExtbase_StorageAdapter_StorageAdapter
     */
    private static $uniqueInstance = NULL;



    /**
     * Class constructor: must not be called directly in order to use getInstance() to get the unique instance of the object
     */
    protected function __construct() {
    }

    
    
    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
     *
     * @param   void
     * @return  Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter      unique instance of the object (Singleton)
     */
    public static function getInstance() {

        if (self::$uniqueInstance === NULL) {
            $className = __CLASS__;
            self::$uniqueInstance = new $className();
        }

        return self::$uniqueInstance;

    }

    
    
    /**
     * Final method to prevent object cloning (using 'clone'), in order to use only the unique instance of the Singleton object.
     * @param   void
     * @return  void
     */
    public final function __clone() {

        throw new Exception('Clone is not allowed for '.get_class($this).' (Singleton)');

    }
    
    
    
    /**
     * Gets the value of a key from the TYPO3 FRONTEND user session (if the session value is serialized it will be returned unserialized)
     *
     * @param   string      name of session key to get the value of
     * @return  mixed       associated value from session
     * @global  object      $GLOBALS['TSFE']->fe_user: tslib_feUserAuth Object
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-09-23
     */
    public function read($key) { 
        
        Tx_PtExtbase_Assertions_Assert::isInstanceOf($GLOBALS['TSFE']->fe_user, 'tslib_feUserAuth', array('message' => 'No valid frontend user found!'));
        
        $val = $GLOBALS['TSFE']->fe_user->getKey('user', $key);
        if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Reading "%s" from FE user session in "$GLOBALS[\'TSFE\']->fe_user"', $key), 'pt_extbase');
        
        if (is_string($val) && unserialize($val) != false) {
            $val = unserialize($val);
        }
        
        return $val;
        
    }
    
    /**
     * Saves a value (objects and arrays will be serialized before) into a session key of the the TYPO3 FRONTEND user session *immediately* (does not wait for complete script execution)
     *
     * @param   string      name of session key to save value into
     * @param   string      value to be saved with session key
     * @return  void
     * @global  object      $GLOBALS['TSFE']->fe_user: tslib_feUserAuth Object
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-09-23
     */
    public function store($key, $val) { 
        
        Tx_PtExtbase_Assertions_Assert::isInstanceOf($GLOBALS['TSFE']->fe_user, 'tslib_feUserAuth', array('message' => 'No valid frontend user found!'));
        
        if (is_object($val) || is_array($val)) {
            $val = serialize($val);
        }
        
        $GLOBALS['TSFE']->fe_user->setKey('user', $key, $val);
        $GLOBALS['TSFE']->fe_user->userData_change = 1;
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Storing "%s" into FE user session using "$GLOBALS[\'TSFE\']->fe_user"', $key), 'pt_extbase');
        
    }
    
    /**
     * Deletes a session value from the TYPO3 FRONTEND user session *immediately* (does not wait for complete script execution)
     *
     * @param   string      name of session key to delete (array key)
     * @return  void
     * @global  object      $GLOBALS['TSFE']->fe_user: tslib_feUserAuth Object
     * @author  Rainer Kuhn <kuhn@punkt.de>
     * @since   2005-09-23
     */
    public function delete($key) { 
        
        Tx_PtExtbase_Assertions_Assert::isInstanceOf($GLOBALS['TSFE']->fe_user, 'tslib_feUserAuth', array('message' => 'No valid frontend user found!'));
        
        unset($GLOBALS['TSFE']->fe_user->uc[$key]);
        $GLOBALS['TSFE']->fe_user->userData_change = 1;
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        if (TYPO3_DLOG) t3lib_div::devLog(sprintf('Deleting "%s" from FE user session in "$GLOBALS[\'TSFE\']->fe_user"', $key), 'pt_extbase');
        
    }
    
    
}

?>