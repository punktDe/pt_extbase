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

#require_once t3lib_extMgm::extPath('pt_extbase') . 'Tests/State/Stubs/SessionAdapterMock.php';

/**
 * Unit tests for session persistence manager
 *
 * @package Tests
 * @subpackage State\Session
 * @author Michael Knoll 
 * @author Daniel Lienert 
 */
class Tx_PtExtbase_Tests_Unit_State_Session_SessionPersistenceManagerTest extends Tx_PtExtbase_Tests_Unit_AbstractBaseTestcase {
	
	/** @test */
	public function classExists() {
		$sessionPersistenceManager = new Tx_PtExtbase_State_Session_SessionPersistenceManager();
		$this->assertTrue(is_a($sessionPersistenceManager, Tx_PtExtbase_State_Session_SessionPersistenceManager));
	}
	
	
	
	/** @test */
	public function persistToSessionPersistsObjectToSession() {
		$persistableObjectStub = new Tx_PtExtbase_Tests_Unit_State_Stubs_PersistableObject();
		$sessionPersistenceManager = Tx_PtExtbase_State_Session_SessionPersistenceManagerFactory::getInstance();
		$sessionPersistenceManager->persistToSession($persistableObjectStub);
	}
	
	
	/** @test */
	public function reloadFromSessionKeepsObjectValues() {
		$persistableObjectStub = new Tx_PtExtbase_Tests_Unit_State_Stubs_PersistableObject();
        $sessionPersistenceManager = Tx_PtExtbase_State_Session_SessionPersistenceManagerFactory::getInstance();
        $persistableObjectStub->initSomeData();
        $sessionPersistenceManager->persistToSession($persistableObjectStub);
        
        $reloadedPersistableObject = new Tx_PtExtbase_Tests_Unit_State_Stubs_PersistableObject();
        $sessionPersistenceManager->loadFromSession($reloadedPersistableObject);
        $this->assertTrue($reloadedPersistableObject->dummyData['testkey1'] == 'testvalue1');
	}
	
	
	
	/** @test */
	public function injectSessionAdapterAcceptsSessionAdapter() {
		$sessionAdapter = Tx_PtExtbase_State_Session_Storage_SessionAdapter::getInstance();
		$sessionPersistenceManager = Tx_PtExtbase_State_Session_SessionPersistenceManagerFactory::getInstance();
		$sessionPersistenceManager->injectSessionAdapter($sessionAdapter);
	}
	
	
	
	/** @test */
	public function getSessionDataByNamespaceReturnsCorrectValue() {
		$sessionAdapterMock = new Tx_PtExtbase_Tests_Unit_State_Stubs_SessionAdapterMock();
		
		$sessionPersistenceManager = Tx_PtExtbase_State_Session_SessionPersistenceManagerFactory::getInstance();
        $sessionPersistenceManager->injectSessionAdapter($sessionAdapterMock);
        $sessionPersistenceManager->init();
        
		$this->assertEquals($sessionPersistenceManager->getSessionDataByNamespace('test1.test2.test3'), 'value');
	}
	
	
	
	/** @test */
	public function getSessionDataHash() {
		$sessionPersistenceManager = $this->getAccessibleMock('Tx_PtExtbase_State_Session_SessionPersistenceManager', array('dummyMethod'), array(),'',FALSE);
		$sessionPersistenceManager->injectSessionAdapter(Tx_PtExtbase_State_Session_Storage_SessionAdapter::getInstance());
		$sessionPersistenceManager->_set('sessionData', array('test'));
		$hash = $sessionPersistenceManager->getSessionDataHash();
		
		$this->assertEquals(md5(serialize(array('test'))), $hash);
	}
	
}

?>