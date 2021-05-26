<?php
namespace PunktDe\PtExtbase\State\Session\Storage;
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

use PunktDe\PtExtbase\Assertions\Assert;
use \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Session Storage Adapter class for TYPO3 FRONTEND _user_ sessions 
 *
 * @author      Rainer Kuhn <kuhn@punkt.de>
 * @author      Michael Knoll <knoll@punkt.de>
 * @package     State
 * @subpackage  Session\Storage
 */
class FeUserSessionAdapter implements AdapterInterface
{
    /**
     * Holds singleton instance of this class
     *
     * @var FeUserSessionAdapter
     */
    private static $uniqueInstance = null;



    /**
     * Class constructor: must not be called directly in order to use getInstance() to get the unique instance of the object
     */
    protected function __construct()
    {

    }

    
    
    /**
     * Returns a unique instance (Singleton) of the object. Use this method instead of the private/protected class constructor.
     *
     * @param   void
     * @return  FeUserSessionAdapter      unique instance of the object (Singleton)
     */
    public static function getInstance()
    {
        if (self::$uniqueInstance === null) {
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
    final public function __clone()
    {
        throw new \Exception('Clone is not allowed for '.get_class($this).' (Singleton)');
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
    public function read($key)
    {
        Assert::isInstanceOf($GLOBALS['TSFE']->fe_user, FrontendUserAuthentication::class, ['message' => 'No valid frontend user found!']);
        
        $val = $GLOBALS['TSFE']->fe_user->getKey('user', $key);
        if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'])) {
            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog(sprintf('Reading "%s" from FE user session in "$GLOBALS[\'TSFE\']->fe_user"', $key), 'pt_extbase');
        }

        if (is_string($val) && unserialize(
                $val,
                [
                    'allowed_classes' => false
                ]
            ) !== false) {
            $val = unserialize(
                $val,
                [
                    'allowed_classes' => false
                ]
            );
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
    public function store($key, $val)
    {
        Assert::isInstanceOf($GLOBALS['TSFE']->fe_user, FrontendUserAuthentication::class, ['message' => 'No valid frontend user found!']);
        
        if (is_object($val) || is_array($val)) {
            $val = serialize($val);
        }
        
        $GLOBALS['TSFE']->fe_user->setKey('user', $key, $val);
        $GLOBALS['TSFE']->fe_user->userData_change = 1;
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'])) {
            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog(sprintf('Storing "%s" into FE user session using "$GLOBALS[\'TSFE\']->fe_user"', $key), 'pt_extbase');
        }
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
    public function delete($key)
    {
        Assert::isInstanceOf($GLOBALS['TSFE']->fe_user, FrontendUserAuthentication::class, ['message' => 'No valid frontend user found!']);
        
        unset($GLOBALS['TSFE']->fe_user->uc[$key]);
        $GLOBALS['TSFE']->fe_user->userData_change = 1;
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        if (isset($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_div.php']['devLog'])) {
            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog(sprintf('Deleting "%s" from FE user session in "$GLOBALS[\'TSFE\']->fe_user"', $key), 'pt_extbase');
        }
    }
}
