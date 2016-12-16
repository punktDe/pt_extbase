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
 * Dummy class implementing session persistable object interface.
 *
 * @package Tests
 * @subpackage State/Stubs
 */
class Tx_PtExtbase_Tests_Unit_State_Stubs_PersistableObject implements Tx_PtExtbase_State_Session_SessionPersistableInterface
{
    /**
     * Some dummy data to be stored in session
     *
     * @var array
     */
    public $dummyData = [];
    
    
    
    /**
     * Fake method to initialize some dummy data
     * 
     * @return void
     */
    public function initSomeData()
    {
        $this->dummyData = ['testkey1' => 'testvalue1', 'testkey2' => 'testvalue2'];
    }
    
    
    
    /**
     * Returns namespace of object to store data in session with
     *
     * @return String Namespace as key to store session data with
     */
    public function getObjectNamespace()
    {
        return 'tests.stateadapter.stubs.persistableobject';
    }



    /**
     * Called by any mechanism to persist an object's state to session
     *
     * @return array Object's state to be persisted to session
     */
    public function _persistToSession()
    {
        return $this->dummyData;
    }



    /**
     * Called by any mechanism to inject an object's state from session
     *
     * @param array $sessionData Object's state previously persisted to session
     */
    public function _injectSessionData(array $sessionData)
    {
        $this->dummyData = $sessionData;
    }
}
