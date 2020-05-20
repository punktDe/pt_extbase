<?php
namespace PunktDe\PtExtbase\State\Session;

use PunktDe\PtExtbase\Context;
use PunktDe\PtExtbase\State\Session\Storage\AdapterInterface;
use PunktDe\PtExtbase\State\Session\Storage\SessionAdapter;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

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
class SessionPersistenceManagerBuilder implements SingletonInterface
{
    /**
     * Holds context in which builder is called
     *
     * @var Context
     */
    protected $context;



    /**
     * Holds singleton instance of session persistence manager once it's been instantiated
     *
     * @var SessionPersistenceManager
     */
    protected $sessionPersistenceManagerInstance;



    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;



    /**
     * Constructor takes context as required dependency to be injected via DI
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(Context $context, ObjectManagerInterface $objectManager)
    {
        $this->context = $context;
        $this->objectManager = $objectManager;
    }



    /**
     * Returns instance of session persistence manager for given session storage adapter.
     * If no storage adapter is given, injected context is used to determine which adapter
     * to use in current context.
     *
     * @param AdapterInterface $sessionStorageAdapter
     * @return SessionPersistenceManager
     * @throws \Exception
     */
    public function getInstance(AdapterInterface $sessionStorageAdapter = null)
    {
        if ($this->sessionPersistenceManagerInstance === null) {
            $this->createInstance($sessionStorageAdapter);
        }

        return $this->sessionPersistenceManagerInstance;
    }



    /**
     * Creates local instance of session persistence manager
     *
     * @param $sessionStorageAdapter
     * @throws \Exception
     */
    protected function createInstance($sessionStorageAdapter)
    {
        if ($sessionStorageAdapter === null) {
            $exception = new \Exception();
            $sessionStorageAdapter = $this->determineSessionStorageAdapterForGivenContext();
        }
        $this->sessionPersistenceManagerInstance = $this->objectManager->get(SessionPersistenceManager::class, $sessionStorageAdapter);
    }



    /**
     * Method determines which session storage adapter to use depending on injected context.
     *
     * @return object
     * @throws \Exception
     */
    protected function determineSessionStorageAdapterForGivenContext()
    {
        if (!$this->context->isInCachedMode()) {
            return GeneralUtility::makeInstance(SessionAdapter::class);
        }
        throw new \Exception('not implemented anymore', 1588601198);
    }
}
