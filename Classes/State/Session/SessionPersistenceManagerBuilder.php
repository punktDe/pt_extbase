<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2012 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll
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
 * Session persistence manager builder
 *
 * Builder returns instance of session persistence manager with a
 * session storage adapter determined by context in which builder
 * is called.
 *
 * @author Michael Knoll
 * @package Tests
 * @subpackage State\Session
 * @see Tx_PtExtbase_Tests_Unit_State_Session_SessionPersistenceManagerBuilderTest
 */
class Tx_PtExtbase_State_Session_SessionPersistenceManagerBuilder implements t3lib_Singleton {

	/**
	 * Holds context in which builder is called
	 *
	 * @var Tx_PtExtbase_Context
	 */
	protected $context;



	/**
	 * Holds singleton instance of session persistence manager once it's been instantiated
	 *
	 * @var Tx_PtExtbase_State_Session_SessionPersistenceManager
	 */
	protected $sessionPersistenceManagerInstance;



	/**
	 * @var Tx_Extbase_Object_ObjectManagerInterface
	 */
	protected $objectManager;



	/**
	 * Constructor takes context as required dependency to be injected via DI
	 *
	 * @param Tx_PtExtbase_Context $context
	 * @param Tx_Extbase_Object_ObjectManagerInterface $objectManager
	 */
	public function __construct(Tx_PtExtbase_Context $context, Tx_Extbase_Object_ObjectManagerInterface $objectManager) {
		$this->context = $context;
		$this->objectManager = $objectManager;
	}



	/**
	 * Returns instance of session persistence manager for given session storage adapter.
	 * If no storage adapter is given, injected context is used to determine which adapter
	 * to use in current context.
	 *
	 * @param Tx_PtExtbase_State_Session_Storage_AdapterInterface $sessionStorageAdapter
	 * @return Tx_PtExtbase_State_Session_SessionPersistenceManager
	 */
	public function getInstance(Tx_PtExtbase_State_Session_Storage_AdapterInterface $sessionStorageAdapter = NULL) {
		if ($this->sessionPersistenceManagerInstance === NULL) {
			$this->createInstance($sessionStorageAdapter);
		}

		return $this->sessionPersistenceManagerInstance;
	}



	/**
	 * Creates local instance of session persistence manager
	 *
	 * @param $sessionStorageAdapter
	 */
	protected function createInstance($sessionStorageAdapter) {
		if ($sessionStorageAdapter === NULL) {
			$exception = new Exception();
			$sessionStorageAdapter = $this->determineSessionStorageAdapterForGivenContext();
		}
		$this->sessionPersistenceManagerInstance = $this->objectManager->get('Tx_PtExtbase_State_Session_SessionPersistenceManager', $sessionStorageAdapter);
	}



	/**
	 * Method determines which session storage adapter to use depending on injected context.
	 */
	protected function determineSessionStorageAdapterForGivenContext() {
		if ($this->context->isInCachedMode()) {
			return Tx_PtExtbase_State_Session_Storage_DBAdapterFactory::getInstance();
		} else {
			return Tx_PtExtbase_State_Session_Storage_SessionAdapter::getInstance();
		}
	}

}
