<?php
namespace PunktDe\PtExtbase\State\Session;
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

use PunktDe\PtExtbase\Lifecycle\EventInterface;
use PunktDe\PtExtbase\Utility\NamespaceUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;

/**
 * Persistence manager to store objects to session and reload objects from session.
 */
class SessionPersistenceManager implements EventInterface
{
    /**
     * Definition of SessionStorageAdapter
     */
    const STORAGE_ADAPTER_NULL = 'Tx_PtExtbase_State_Session_Storage_NullStorageAdapter';
    const STORAGE_ADAPTER_DB = 'Tx_PtExtbase_State_Session_Storage_DBAdapter';
    const STORAGE_ADAPTER_FEUSER_SESSION = 'Tx_PtExtbase_State_Session_Storage_FeUserSessionAdapter';
    const STORAGE_ADAPTER_BROWSER_SESSION = 'Tx_PtExtbase_State_Session_Storage_SessionAdapter';

    /**
     * Holds an instance for a session adapter to store data to session
     *
     * @var Tx_PtExtbase_State_Session_Storage_SessionAdapter
     */
    private $sessionAdapter = null;

    /**
     * Holds cached session data.
     *
     * @var array
     */
    protected $sessionData = [];


    /**
     * HashKey identifies sessionData
     *
     * @var string
     */
    protected $sessionHash = null;


    /**
     * Holds an array of objects that should be persisted when lifecycle ends
     *
     * @var array<Tx_PtExtbase_State_Session_SessionPersistableInterface>
     */
    protected $objectsToPersist = [];


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
     * Constructor takes required adapter interface to be used for session storage
     *
     * @param Tx_PtExtbase_State_Session_Storage_AdapterInterface $sessionAdapter
     */
    public function __construct(Tx_PtExtbase_State_Session_Storage_AdapterInterface $sessionAdapter)
    {
        $this->sessionAdapter = $sessionAdapter;
        $this->sessionAdapaterClass = get_class($sessionAdapter);
    }


    /**
     * Initializes this object by reading session data
     *
     * @return void
     */
    public function init()
    {
        $this->readFromSession();

        $this->isInitialized = true;
    }


    /**
     * Persists a given object to session
     *
     * @param Tx_PtExtbase_State_Session_SessionPersistableInterface $object
     * @throws Exception if session hash has been already calculated and session data has changed
     */
    public function persistToSession(Tx_PtExtbase_State_Session_SessionPersistableInterface $object)
    {
        $sessionNamespace = $object->getObjectNamespace();

        if ($this->sessionAdapaterClass == self::STORAGE_ADAPTER_DB
            && $this->sessionHash != null && $this->sessionHash != md5(serialize($this->sessionData))
        ) {
            throw new Exception('Session Hash already calculated and current sessiondata changed!! 1293004344' . $sessionNamespace . ': Calc:' . $this->sessionHash . ' NEW: ' . md5(serialize($this->sessionData)));
        }

        Tx_PtExtbase_Assertions_Assert::isNotEmptyString($sessionNamespace, ['message' => 'Object namespace must not be empty! 1278436822']);
        $objectData = $object->_persistToSession();

        if ($this->sessionData == null) {
            $this->sessionData = [];
        }

        if ($objectData) {
            $this->sessionData = NamespaceUtility::saveDataInNamespaceTree($sessionNamespace, $this->sessionData, $objectData);
        }

        // Remove session values, if object data is null or empty array
        if ($objectData === null || count($objectData) == 0) {
            $this->sessionData = NamespaceUtility::removeDataFromNamespaceTree($sessionNamespace, $this->sessionData);
        }
    }


    /**
     * Loads session data into given object
     *
     * @param Tx_PtExtbase_State_Session_SessionPersistableInterface $object Object to inject session data into
     */
    public function loadFromSession(Tx_PtExtbase_State_Session_SessionPersistableInterface $object)
    {
        $objectData = $this->getSessionDataForObjectNamespace($object->getObjectNamespace());
        if (is_array($objectData)) {
            $object->_injectSessionData($objectData);
        }
    }


    /**
     * Get the session data for object
     *
     * @param string $objectNamespace
     * @return array sessiondata
     */
    public function getSessionDataForObjectNamespace($objectNamespace)
    {
        Tx_PtExtbase_Assertions_Assert::isNotEmptyString($objectNamespace, ['message' => 'object namespace must not be empty! 1278436823']);
        return NamespaceUtility::getArrayContentByArrayAndNamespace($this->sessionData, $objectNamespace);
    }


    /**
     * Persist the cached session data.
     *
     */
    public function persist()
    {
        $this->persistObjectsToSession();
        $this->sessionAdapter->store('pt_extbase.cached.session', $this->sessionData);
    }


    /**
     * Read the session data into the cache.
     */
    protected function readFromSession()
    {
        $this->sessionData = $this->sessionAdapter->read('pt_extbase.cached.session');
    }


    /**
     * React on lifecycle events.
     *
     * @param integer $state
     */
    public function lifecycleUpdate($state)
    {
        switch ($state) {
            case \PunktDe\PtExtbase\Lifecycle\Manager::START:
                if (!$this->isInitialized) {
                    $this->init();
                }
                break;
            case \PunktDe\PtExtbase\Lifecycle\Manager::END:
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
    public function getSessionDataByNamespace($objectNamespace)
    {
        return NamespaceUtility::getArrayContentByArrayAndNamespace($this->sessionData, $objectNamespace);
    }


    /**
     * Remove session data by given namespace
     *
     * @param string $namespaceString
     */
    public function removeSessionDataByNamespace($namespaceString)
    {
        $this->sessionData = NamespaceUtility::removeDataFromNamespaceTree($namespaceString, $this->sessionData);
    }


    /**
     * Return the hash of the currently set sessiondata
     * After this method is called, it is not allowed to manipulate the session data
     *
     * @return string hash
     */
    public function getSessionDataHash()
    {
        if ($this->sessionHash == null) {
            $this->lifecycleUpdate(\PunktDe\PtExtbase\Lifecycle\Manager::END);
            $this->sessionHash = md5(serialize($this->sessionData));
        }
        return $this->sessionHash;
    }


    /**
     * Loads and registers an object on session manager
     *
     * @param Tx_PtExtbase_State_Session_SessionPersistableInterface $object
     */
    public function registerObjectAndLoadFromSession(Tx_PtExtbase_State_Session_SessionPersistableInterface $object)
    {
        $this->loadFromSession($object);
        $this->registerObjectForSessionPersistence($object);
    }


    /**
     * Registers an object to be persisted to session when lifecycle ends
     *
     * @param Tx_PtExtbase_State_Session_SessionPersistableInterface $object
     */
    public function registerObjectForSessionPersistence(Tx_PtExtbase_State_Session_SessionPersistableInterface $object)
    {
        if (!in_array(spl_object_hash($object), $this->objectsToPersist)) {
            $this->objectsToPersist[spl_object_hash($object)] = $object;
        }
    }


    /**
     * Persists all objects registered for session persistence
     *
     */
    protected function persistObjectsToSession()
    {
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
    public function addSessionRelatedArguments(&$argumentArray)
    {
        if (!is_array($argumentArray)) {
            $argumentArray = [];
        }

        if ($this->sessionAdapaterClass === self::STORAGE_ADAPTER_DB) {
            $argumentArray['state'] = $this->getSessionDataHash();
        } elseif ($this->sessionAdapaterClass === self::STORAGE_ADAPTER_NULL) {
            $this->lifecycleUpdate(\PunktDe\PtExtbase\Lifecycle\Manager::END);
            $sessionArguments = $this->array_filter_recursive($this->sessionData);
            ArrayUtility::mergeRecursiveWithOverrule($sessionArguments, $argumentArray);
            $argumentArray = $sessionArguments;
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
    protected function array_filter_recursive($array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = $this->array_filter_recursive($value);
            }
        }

        return array_filter($array);
    }


    /**
     * Resets session data
     *
     * @param Tx_PtExtbase_State_GpVars_GpVarsAdapter $gpVarManager
     * @return void
     */
    public function resetSessionDataOnEmptyGpVars(Tx_PtExtbase_State_GpVars_GpVarsAdapter $gpVarManager)
    {
        if ($gpVarManager->isEmptySubmit()) {
            $this->sessionData = [];
        }
    }


    /**
     * @return array
     */
    public function getSessionData()
    {
        return $this->sessionData;
    }


    /**
     * @param array $sessionData
     */
    public function setSessionData(array $sessionData)
    {
        $this->sessionData = $sessionData;
        $this->sessionHash = null;
    }


    public function resetSessionData()
    {
        $this->sessionData = [];
    }
}
