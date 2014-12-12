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
 * Class implements adapter to store the plugins state into the database 
 * 
 * @author Daniel Lienert 
 * @package Domain
 * @subpackage State\Session\Storage
 */
class Tx_PtExtbase_State_Session_Storage_DBAdapter implements Tx_PtExtbase_State_Session_Storage_AdapterInterface {

	
	/**
	 * @var t3lib_cache_frontend_Cache
	 */
	protected $stateCache;
	
	
	/**
	 * @var Tx_PtExtbase_State_Session_SessionPersistenceManager
	 */
	protected $sessionPersistanceManager;
	
	
	
	/**
	 * MD5 sum identifying the state to load from database 
	 * 
	 * @var string
	 */
	protected $stateHash;

	
	
	/**
	 * Tag the cache entrys with current extension name
	 * 
	 * @var string
	 */
	protected $cacheTag = 'untagged'; 
	
	
	
	/**
	 * Factory method to get an instance of this class.
	 * 
	 * We use factory method of factory here to create the instance to preserve interface for 
	 * creating storage adapters.
	 *
	 * @return Tx_PtExtbase_State_Session_Storage_DBAdapter
	 */
	public static function getInstance() {
		return Tx_PtExtbase_State_Session_Storage_DBAdapterFactory::getInstance();
	}
	
	
	
	/**
	 * Init the cache storage adapter
	 * 
	 */
	public function init() {
		$this->cacheTag = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Tx_Extbase_Object_ObjectManager')->get('Tx_PtExtbase_Context')->getExtensionName();
	}
	
	
	
	/**
	 * Inject the state cache
	 * 
	 * @param $stateCache
	 */
	public function injectStateCache($stateCache) {
		$this->stateCache = $stateCache;
	}
	
	
	
	/**
	 * Inject the sessionPersistanceManager
	 * 
	 * @param Tx_PtExtbase_State_Session_SessionPersistenceManager $sessionPersistanaceManager
	 */
	public function injectSessionPersistanceManager(Tx_PtExtbase_State_Session_SessionPersistenceManager $sessionPersistanaceManager) {
		$this->sessionPersistanceManager = $sessionPersistanaceManager;
	}
	
	
	
	/**
	 * Set the statehash 
	 * 
	 * @param string $stateHash
	 */
	public function setStateHash($stateHash) {
		$this->stateHash = $stateHash;
	}

	
	
	/**
	 * Retrieve a state dataObject from the repository and return the requested value
	 * 
	 * @param string $key
	 */
	public function read($key) {
		if(!$this->stateHash) {
			return NULL;
		}
		
		if($this->stateCache->has($this->stateHash)) {
			$stateData = unserialize($this->stateCache->get($this->stateHash));
		}
		
		return $stateData[$key];
	}
	
	
	
	/**
	 * Save a value to state data
	 * 
	 * @param string $key
	 * @param string $value
	 */
	public function store($key, $value) {
		/* TODO: ptextbase save only one value to the session when the lifecycle ends (the internal session cache)
		 * because of that, the session hash is used in links before the session is written to database. that means, in this
		 * mode only one value can be written to the session (Daniel)
		 */
		//$stateData = $this->state->getStateDataAsArray();
		$stateData[$key] = $value;
		
		$stateHash = md5(serialize($value));

		$this->stateCache->set($stateHash, serialize($stateData), array($this->cacheTag), 0);
	}
	
	
	
	/**
	 * Remove a value from state data
	 * 
	 * @param string $key
	 */
	public function delete($key) {
		
		if($this->stateCache->has($this->stateHash)) {
			$stateData = unserialize($this->stateCache->get($this->stateHash));
		}
		
		unset($stateData[$key]);
		$this->stateCache->set($stateHash, serialize($stateData), array($this->cacheTag), 0);
	}
}
?>