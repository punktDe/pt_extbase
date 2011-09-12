<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
 *  All rights reserved
 *
 *  For further information: http://extlist.punkt.de <extlist@punkt.de>
 *
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
 * Class implements a factory for session persistence manager.
 *
 * @package Domain
 * @subpackage StateAdapter
 * @author Michael Knoll 
 */
class Tx_PtExtbase_State_Session_SessionPersistenceManagerFactory {
	
	/**
	 * Singleton instance of session persistence manager object
	 *
	 * @var Tx_PtExtbase_State_Session_SessionPersistenceManager
	 */
	private static $instance = NULL;
	
	
	
	/**
	 * Factory method for session persistence manager 
	 * 
	 * @param Tx_PtExtbase_State_Session_Storage_AdapterInterface $storageAdapter Storage adapter to be used for session persistence manager
	 * @return Tx_PtExtbase_State_Session_SessionPersistenceManager Singleton instance of session persistence manager 
	 */
	public static function getInstance(Tx_PtExtbase_State_Session_Storage_AdapterInterface $storageAdapter = null) {
		if (self::$instance == NULL) {
			self::$instance = new Tx_PtExtbase_State_Session_SessionPersistenceManager();
			
			// TODO factory should decide, which storage adapter to use!
			if ($storageAdapter === null) {
			   self::$instance->injectSessionAdapter(self::getStorageAdapter());
			} else {
				self::$instance->injectSessionAdapter($storageAdapter);
			}
		}
		return self::$instance;
	}
	
	
	
	/**
	 * Initialize the sessionAdapter
	 *
	 * @return Tx_PtExtbase_State_Session_Storage_AdapterInterface storageAdapter
	 */
	private static function getStorageAdapter() {
		
		if(t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager')->get('Tx_PtExtbase_Context')->isInCachedMode()) {
			return Tx_PtExtbase_State_Session_Storage_DBAdapterFactory::getInstance();	
		} else {
			return Tx_PtExtbase_State_Session_Storage_SessionAdapter::getInstance();	
		}
	}
	
}

?>