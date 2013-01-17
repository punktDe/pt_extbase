<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
 *  Authors: Daniel Lienert, Michael Knoll, Christoph Ehscheidt
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
 * Persistence manager to store objects to session and reload objects from session.
 *
 * @package State
 * @subpackage Session
 * @author Daniel Lienert 
 * @author Michael Knoll 
 */
class Tx_PtExtbase_State_Session_SessionPersistenceManager implements Tx_PtExtbase_Lifecycle_EventInterface {

	/**
	 * Definition of SessionStorageAdapter
	 * 
	 * TODO remove pt_extlist and pt_tools where they are no longer available!
	 */
	const STORAGE_ADAPTER_NULL = 'Tx_PtExtbase_State_Session_Storage_NullStorageAdapter';
	const STORAGE_ADAPTER_DB = 'Tx_PtExtbase_State_Session_Storage_DBAdapter';
	const STORAGE_ADAPTER_FEUSER_SESSION = 'Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter';
	const STORAGE_ADAPTER_BROWSER_SESSION = 'Tx_PtExtbase_State_Session_Storage_SessionAdapter';
	
	
	
	/**
	 * 
	 */
	private $internalSessionState = Tx_PtExtbase_Lifecycle_Manager::UNDEFINED;
	
	
	
	/**
	 * Holds an instance for a session adapter to store data to session
	 * 
	 * @var Tx_PtExtbase_State_Session_StorageAdapter
	 */
	private $sessionAdapter = null;
	
	
	
	/**
	 * Holds cached session data.
	 * 
	 * @var array
	 */
	protected $sessionData = array();
	
	
	
	/**
	 * HashKey identifies sessionData
	 * 
	 * @var string
	 */
	protected $sessionHash = NULL;
	
	
	
	/**
	 * Holds an array of objects that should be persisted when lifecycle ends
	 *
	 * @var array<Tx_PtExtbase_State_Session_SessionPersistableInterface>
	 */
	protected $objectsToPersist = array();
    
    
    
    /**
     * Identifies the session storage adapter
     * @var string
     */
    protected $sessionAdapaterClass;



    /**
     * Set to true, if session persistence manager had been initialized before
     *
     * @var bool
     */
    protected $isInitialized = false;

	
	
	/**
	 * Injector for session adapter
	 *
	 * @param Tx_PtExtbase_State_Session_Storage_AdapterInterface $sessionAdapter
	 */
	public function injectSessionAdapter(Tx_PtExtbase_State_Session_Storage_AdapterInterface $sessionAdapter) {
		$this->sessionAdapter = $sessionAdapter;
      $this->sessionAdapaterClass = get_class($sessionAdapter);
	}



    /**
     * Initializes this object by reading session data
     * 
     * @return void
     */
    public function init() {
        $this->readFromSession();

        $this->isInitialized = true;
    }


	
	
	/**
	 * Persists a given object to session
	 *
	 * @param Tx_PtExtbase_State_Session_SessionPersistableInterface $object
	 */
	public function persistToSession(Tx_PtExtbase_State_Session_SessionPersistableInterface $object) {
		$sessionNamespace = $object->getObjectNamespace();

		if ($this->sessionAdapaterClass == self::STORAGE_ADAPTER_DB
			 && $this->sessionHash != NULL && $this->sessionHash != md5(serialize($this->sessionData))
		) {
			throw new Exception('Session Hash already calculated and current sessiondata changed!! 1293004344' . $sessionNamespace . ': Calc:' . $this->sessionHash . ' NEW: ' . md5(serialize($this->sessionData)));
		}

		Tx_PtExtbase_Assertions_Assert::isNotEmptyString($sessionNamespace, array('message' => 'Object namespace must not be empty! 1278436822'));
		$objectData = $object->persistToSession();

		if ($this->sessionData == NULL) {
			$this->sessionData = array();
		}
		
		if ($objectData) {
			$this->sessionData = Tx_PtExtbase_Utility_NameSpace::saveDataInNamespaceTree($sessionNamespace, $this->sessionData, $objectData);
		}

		// Remove session values, if object data is null or empty array
		if ($objectData === null || count($objectData) == 0) {
			$this->sessionData = Tx_PtExtbase_Utility_NameSpace::removeDataFromNamespaceTree($sessionNamespace, $this->sessionData);
		}
	}

	
	
	/**
	 * Loads session data into given object
	 *
	 * @param Tx_PtExtbase_State_Session_SessionPersistableInterface $object   Object to inject session data into
	 */
	public function loadFromSession(Tx_PtExtbase_State_Session_SessionPersistableInterface $object) {
		$objectData = $this->getSessionDataForObjectNamespace($object->getObjectNamespace());
		if (is_array($objectData)) {
			$object->injectSessionData($objectData);
		}
	}
	
	
	
	/**
	 * Get the session data for object 
	 * @param string $objectNameSpace
	 * @return array sessiondata
	 */
	public function getSessionDataForObjectNamespace($objectNamespace) {
		Tx_PtExtbase_Assertions_Assert::isNotEmptyString($objectNamespace, array('message' => 'object namespace must not be empty! 1278436823'));
		return Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($this->sessionData, $objectNamespace);
	}
	
	
	
	/**
	 * Persist the cached session data.
	 * 
	 */
	public function persist() {
		$this->persistObjectsToSession();
		$this->sessionAdapter->store('pt_extbase.cached.session', $this->sessionData);
	}
	
	
	
	/**
	 * Read the session data into the cache.
	 *
     * TODO rename this to readFromSessionAdapter!
	 */
	protected function readFromSession() {
		$this->sessionData = $this->sessionAdapter->read('pt_extbase.cached.session');
	}
	
	
	
	/**
	 * React on lifecycle events.
	 * 
	 * @param int $state
	 */
	public function lifecycleUpdate($state) {
		switch($state) {
			case Tx_PtExtbase_Lifecycle_Manager::START:
				if (!$this->isInitialized) $this->init();
				break;
			case Tx_PtExtbase_Lifecycle_Manager::END:
				$this->persist();
				break;	
		}
	}

	
	
	/**
	 * Returns data from session for given namespace
	 * 
	 * @param string $objectNamespace
	 * @return array
	 */
	public function getSessionDataByNamespace($objectNamespace) {
		return Tx_PtExtbase_Utility_NameSpace::getArrayContentByArrayAndNamespace($this->sessionData, $objectNamespace);
	}
	
	
	
	/**
	 * Remove session data by given namespace
	 * 
	 * @param $namespaceString string
	 */
    public function removeSessionDataByNamespace($namespaceString) {
        Tx_PtExtbase_Utility_NameSpace::removeDataFromNamespaceTree($namespaceString, $this->sessionData);
    }
	
	
	
	/**
	 * Return the hash of the currently set sessiondata
	 * After this method is called, it is not allowed to manipulate the session data
	 * 
	 * @return string hash
	 */
	public function getSessionDataHash() {
		if($this->sessionHash == NULL) {
			$this->lifecycleUpdate(Tx_PtExtbase_Lifecycle_Manager::END);
			$this->sessionHash = md5(serialize($this->sessionData));
		}
		return $this->sessionHash;
	}
	
	
	
    /**
     * Loads and registers an object on session manager
     *
     * @param Tx_PtExtbase_State_Session_SessionPersistableInterface $object
     */
    public function registerObjectAndLoadFromSession(Tx_PtExtbase_State_Session_SessionPersistableInterface $object) {
    	$this->loadFromSession($object);
    	$this->registerObjectForSessionPersistence($object);
    }
	
    
    
    /**
     * Registers an object to be persisted to session when lifecycle ends
     *
     * @param Tx_PtExtbase_State_Session_SessionPersistableInterface $object
     */
    public function registerObjectForSessionPersistence(Tx_PtExtbase_State_Session_SessionPersistableInterface $object) {
        if (!in_array(spl_object_hash($object), $this->objectsToPersist)) {
    		$this->objectsToPersist[spl_object_hash($object)] = $object;
    	}
    }
    
    
	
	/**
     * Persists all objects registered for session persistence
     * 
     */
	protected function persistObjectsToSession() {
		foreach ($this->objectsToPersist as $objectToPersist) {
			/* @var $objectToPersist Tx_PtExtbase_State_Session_SessionPersistableInterface */
			if (!is_null($objectToPersist)) { // object reference could be null in the meantime
				$this->persistToSession($objectToPersist);
			}
		}
	}


	/**
	 * Add arguments to url if we cannot use session.
	 *
	 * This happens, if we want to use caching for example. All
	 * session persisted values are then transported via URL.
	 *
	 * @param array $argumentArray
	 */
	public function addSessionRelatedArguments(&$argumentArray) {
		if (!is_array($argumentArray)) $argumentArray = array();

		if ($this->sessionAdapaterClass == self::STORAGE_ADAPTER_DB) {
			$argumentArray['state'] = $this->getSessionDataHash();

		} elseif ($this->sessionAdapaterClass == self::STORAGE_ADAPTER_NULL) {
			$this->lifecycleUpdate(Tx_PtExtbase_Lifecycle_Manager::END);
			$additionalArgumens = $this->array_filter_recursive($this->sessionData);
			$argumentArray = t3lib_div::array_merge_recursive_overrule($additionalArgumens, $argumentArray);
		}
	}


	/**
	 *  This method recursively filters all entries that are NULL and removes
	 *  empty arrays. This is needed to not add unneeded data to the session (or to the URL Parameter)
	 *
	 *
	 * @param $array
	 * @return array
	 */
	protected function array_filter_recursive($array) {
		foreach ($array as &$value) {
			if (is_array($value)) {
				$value = $this->array_filter_recursive($value);
			}
		}

		return array_filter($array);
	}



	/**
	 * Resets session data, if given gpVarAdapter has empty submit values (no gpvars are given for current request)
	 *
	 * @param Tx_PtExtbase_State_GpVars_GpVarsAdapter $gpVarManager
	 * @return void
	 */
	public function resetSessionDataOnEmptyGpVars(Tx_PtExtbase_State_GpVars_GpVarsAdapter $gpVarManager) {
		if ($gpVarManager->isEmptySubmit()) {
			$this->sessionData = array();
		}
    }
     
}
?>
