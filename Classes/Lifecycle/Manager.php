<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010-2011 punkt.de GmbH - Karlsruhe, Germany - http://www.punkt.de
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

namespace PunktDe\PtExtbase\Lifecycle;

/**
 * Lifecycle Manager allowes to register class wich will be notified 
 * on different lifecycle envents.
 * 
 * @author Christoph Ehscheidt 
 * @author Michael Knoll
 * @package Lifecycle 
 */
class Manager implements \TYPO3\CMS\Core\SingletonInterface
{
    const UNDEFINED = 0;
    const START = 1;
    const END = 10;

    
    
    /**
     * Holds the state of the lifecycle.
     * 
     * @var integer
     */
    protected $state;
    
    
    
    /**
     * Holds all observers which need to be updated.
     * 
     * @var array
     */
    protected $observers = [];
    
    
    
    /**
     * Constructor for lifecycle manager
     *
     * After construction, stat is UNDEFINED
     */
    public function __construct()
    {
        $this->state = self::UNDEFINED;
    }



    /**
     * Used to initialize object when instantiated via object manager
     */
    public function initializeObject()
    {
        $this->state = self::START;
    }
    
    
    
    /**
     * Returns the current state of the lifecycle.
     * 
     * @return integer
     */
    public function getState()
    {
        return $this->state;
    }
    
    
    
    /**
     * Updates the current lifecycle state. 
     * If given state is not a advanced state, nothing will happen.
     * 
     * @param integer $state
     */
    public function updateState($state)
    {
        if ($state <= $this->state) {
            return;
        }

        $this->state = $state;
        $this->fireUpdate();
    }
    
    
    
    /**
     * Register a lifecycle observer.
     * 
     * @param Tx_PtExtbase_Lifecycle_EventInterface $observer
     * @param bool $static Override existing observer of same class.
     */
    public function register(Tx_PtExtbase_Lifecycle_EventInterface $observer, $static = true)
    {
        if ($static) {
            $this->observers[get_class($observer)] = $observer;
        } else {
            $this->observers[] = $observer;
        }
    }
    
    
    
    /**
     * Registers a lifecycle observer and updates state on registered object
     *
     * @param Tx_PtExtbase_Lifecycle_EventInterface $observer
     * @param bool $static Override existing observer of same class.
     */
    public function registerAndUpdateStateOnRegisteredObject(Tx_PtExtbase_Lifecycle_EventInterface $observer, $static = true)
    {
        $this->register($observer, $static);
        $observer->lifecycleUpdate($this->getState());
    }
    
    
    
    /**
     * Notifies observers about state being updated
     *
     */
    protected function fireUpdate()
    {
        foreach ($this->observers as $observer) { /* @var $observer Tx_PtExtbase_Lifecycle_EventInterface */
            $observer->lifecycleUpdate($this->state);
        }
    }
}
