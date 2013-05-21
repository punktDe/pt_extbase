<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
 *  All rights reserved
 *
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
 * Factory for the DB storage adapter
 * 
 * @author Daniel Lienert 
 * @package Domain
 * @subpackage State\Session\Storage
 */
class Tx_PtExtbase_State_Session_Storage_DBAdapterFactory {
	
	/**
	 *
	 * @var Tx_PtExtbase_State_Session_Storage_DBAdapter
	 */
	protected static $instance = NULL;
	
	
	/**
	 * Create a single instance of the db storage adapter
	 * 
	 * @return Tx_PtExtbase_State_Session_Storage_DBAdapter
	 */
	public static function getInstance() {
		
		if(self::$instance == NULL) {
			self::$instance = new Tx_PtExtbase_State_Session_Storage_DBAdapter();

			self::$instance->injectStateCache(self::buildStateCache());
			self::$instance->setStateHash(self::getStateHash());	
			
			self::$instance->init();
		}
		
		return self::$instance;
	}
	
	
	
	/**
	 * Build TYPO3 Caching Framework Cache
	 * 
	 * @return t3lib_cache_frontend_Cache
	 */
	protected function buildStateCache() {
		/**
		 * TODO this is broken and we only use it to prevent a fatal error
		 * TODO fix this if you want to use caching!!!
		 */
		if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_ptextbase']['options'])) {
			$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_ptextbase'] = array(
				'frontend' => 't3lib_cache_frontend_VariableFrontend',
				'backend' => 't3lib_cache_backend_DbBackend',
				'options' => array(
					'cacheTable' => 'tx_ptextbase_cache_state',
					'tagsTable'  => 'tx_ptextbase_cache_state_tags',
				)
			);
		}

		// Create the cache
		try {
			$GLOBALS['typo3CacheFactory']->create(
				'tx_ptextbase',
				't3lib_cache_frontend_VariableFrontend',
				$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_ptextbase']['backend'],
				$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['tx_ptextbase']['options']
			);
		} catch(t3lib_cache_exception_DuplicateIdentifier $e) {
			// do nothing, the cache already exists
		}

		// Initialize the cache
		try {
			$cache = $GLOBALS['typo3CacheManager']->getCache('tx_ptextbase');
		} catch(t3lib_cache_exception_NoSuchCache $e) {
			throw new Exception('Unable to load Cache! 1299942198');
		}


		return $cache;
	}
	
	
	
	/**
	 * Get the stateHash.
	 * 
	 * @return string hash 
	 */
	protected static function getStateHash() {
		$getPostVarAdapter = Tx_PtExtbase_State_GpVars_GpVarsAdapterFactory::getInstance($extensionNameSpace);
		$stateHash = $getPostVarAdapter->getParametersByNamespace('state');
		return $stateHash;	
	}
}
?>